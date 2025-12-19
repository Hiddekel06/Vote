<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoteEvent;
use App\Models\VoteJourJ;
use App\Models\Vote;
use App\Models\Projet;
use App\Models\Secteur;
use App\Helpers\GeoHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VoteJourJController extends Controller
{
    /**
     * Affiche la page de vote Jour J
     * Accès direct - affiche toujours les projets comme les pages de vote normales
     */
    public function show(Request $request)
    {
        // Récupérer un événement actif pour la validation GPS (optionnel)
        $event = VoteEvent::where('is_active', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->latest('created_at')
            ->first();

        // Filtres URL
        $profileType = $request->query('profile_type'); // 'student' | 'startup' | 'other' | null
        $search = trim($request->query('search', ''));

        // IDs des projets finalistes
        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->where('is_finaliste', 1)
            ->pluck('projet_id');

        // Construire la requête secteurs
        $secteurQuery = Secteur::query();

        // N'afficher que les secteurs qui ont des projets finalistes (et éventuellement du bon profil)
        $secteurQuery->whereHas('projets', function ($projetQuery) use ($preselectedProjectIds, $profileType) {
            $projetQuery->whereIn('id', $preselectedProjectIds);
            if (in_array($profileType, ['student', 'startup', 'other'])) {
                $projetQuery->whereHas('submission', function ($submissionQuery) use ($profileType) {
                    $submissionQuery->where('profile_type', $profileType);
                });
            }
        });

        // Filtre de recherche sur le nom du secteur ou les projets
        if ($search !== '') {
            $secteurQuery->where(function ($q) use ($search, $preselectedProjectIds, $profileType) {
                $q->where('nom', 'like', '%' . $search . '%')
                  ->orWhereHas('projets', function ($subQuery) use ($search, $preselectedProjectIds, $profileType) {
                      $subQuery->whereIn('id', $preselectedProjectIds)
                          ->when(in_array($profileType, ['student', 'startup', 'other']), function ($qq) use ($profileType) {
                              $qq->whereHas('submission', fn($s) => $s->where('profile_type', $profileType));
                          })
                          ->where(function ($subSubQuery) use ($search) {
                              $subSubQuery->where('nom_projet', 'like', '%' . $search . '%')
                                          ->orWhere('nom_equipe', 'like', '%' . $search . '%');
                          });
                  });
            });
        }

        // Charger les projets des secteurs, avec les mêmes filtres
        $secteurQuery->with(['projets' => function ($projetQuery) use ($preselectedProjectIds, $profileType, $search) {
            $projetQuery->whereIn('projets.id', $preselectedProjectIds)
                ->with(['submission', 'listePreselectionne'])
                ->when(in_array($profileType, ['student', 'startup', 'other']), function ($qq) use ($profileType) {
                    $qq->whereHas('submission', fn($s) => $s->where('profile_type', $profileType));
                })
                ->when($search !== '', function ($qq) use ($search) {
                    $qq->where(function ($sub) use ($search) {
                        $sub->where('nom_projet', 'like', '%' . $search . '%')
                            ->orWhere('nom_equipe', 'like', '%' . $search . '%');
                    });
                })
                ->orderBy('nom_projet');
        }]);

        $secteurs = $secteurQuery->orderBy('nom')->get();

        return view('vote-jour-j', compact('secteurs', 'event'));
    }

    /**
     * Traite le vote Jour J (avec vérification GPS + OTP)
     * Sécurité : OTP valide + localisation dans le rayon
     */
    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'telephone' => 'required|string',
            'code_otp' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Récupérer l'événement actif
        $event = VoteEvent::where('is_active', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->latest('created_at')
            ->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun événement actif pour le moment.'
            ], 403);
        }

        // Vérifier l'OTP (réutilisation du code existant)
        $otpController = new \App\Http\Controllers\OrangeSmsController();
        $otpValidation = $otpController->verifyOtp($request->telephone, $request->code_otp);

        if (!$otpValidation) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide ou expiré.'
            ], 422);
        }

        // Si un événement existe, vérifier la géolocalisation
        $distance = null;
        if ($event) {
            // Calculer la distance avec Haversine
            $distance = GeoHelper::haversineDistance(
                $event->latitude,
                $event->longitude,
                $request->latitude,
                $request->longitude
            );

            // Vérifier que l'utilisateur est dans la zone
            if ($distance > $event->rayon_metres) {
                // Créer un enregistrement avec statut 'outside_zone' pour audit
                VoteJourJ::create([
                    'vote_id' => null,
                    'vote_event_id' => $event->id,
                    'latitude_user' => $request->latitude,
                    'longitude_user' => $request->longitude,
                    'distance_metres' => $distance,
                    'qr_token_used' => null,
                    'qr_token_expires_at' => null,
                    'validation_status' => 'outside_zone',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => "Vous devez être dans le périmètre de la salle pour voter. Distance: {$distance}m (maximum: {$event->rayon_metres}m)"
                ], 403);
            }
        }

        // Vérifier que l'utilisateur n'a pas déjà voté pour ce projet
        $existingVote = Vote::where('telephone', $request->telephone)
            ->where('projet_id', $request->projet_id)
            ->first();

        if ($existingVote) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà voté pour ce projet.'
            ], 422);
        }

        // Créer le vote
        $vote = Vote::create([
            'projet_id' => $request->projet_id,
            'telephone' => $request->telephone,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Créer l'enregistrement VoteJourJ avec validation success (seulement si un événement existe)
        if ($event) {
            VoteJourJ::create([
                'vote_id' => $vote->id,
                'vote_event_id' => $event->id,
                'latitude_user' => $request->latitude,
                'longitude_user' => $request->longitude,
                'distance_metres' => $distance,
                'qr_token_used' => null,
                'qr_token_expires_at' => null,
                'validation_status' => 'success',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vote enregistré avec succès ! La délibération aura lieu dans quelques instants.'
        ]);
    }

    /**
     * ADMIN : Affiche le formulaire de création d'événement Jour J
     */
    public function createEvent()
    {
        return view('admin.vote-events.create');
    }

    /**
     * ADMIN : Page de gestion des événements Jour J
     */
    public function indexEvents()
    {
        $events = VoteEvent::with('voteJourJ.vote.projet')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                // Compter les votes Jour J réussis pour cet événement
                $successfulVotes = $event->voteJourJ()
                    ->where('validation_status', 'success')
                    ->with('vote.projet')
                    ->get();

                // Créer le classement des projets pour cet événement
                $ranking = $successfulVotes
                    ->groupBy('vote.projet_id')
                    ->map(function ($votes) {
                        return [
                            'projet' => $votes->first()->vote->projet,
                            'vote_count' => $votes->count()
                        ];
                    })
                    ->sortByDesc('vote_count')
                    ->values();

                // Total votes pour cet événement
                $totalVotes = $successfulVotes->count();

                return [
                    'event' => $event,
                    'total_votes' => $totalVotes,
                    'ranking' => $ranking
                ];
            });

        return view('admin.vote-events.index', compact('events'));
    }

    /**
     * ADMIN : Créer un nouvel événement Jour J
     */
    public function storeEvent(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'rayon_metres' => 'required|integer|min:10|max:1000',
            'date_debut' => 'required|date_format:Y-m-d\TH:i',
            'date_fin' => 'required|date_format:Y-m-d\TH:i|after:date_debut',
        ]);

        // Générer un secret unique pour le QR code
        $qrSecret = bin2hex(random_bytes(32));

        $event = VoteEvent::create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'rayon_metres' => $request->rayon_metres,
            'qr_secret' => $qrSecret,
            'is_active' => true, // Actif par défaut
            'date_debut' => Carbon::createFromFormat('Y-m-d\TH:i', $request->date_debut),
            'date_fin' => Carbon::createFromFormat('Y-m-d\TH:i', $request->date_fin),
        ]);

        return redirect()->route('admin.vote-events.index')
            ->with('success', '✅ Événement créé avec succès ! Secret QR: ' . substr($qrSecret, 0, 8) . '...');
    }

    /**
     * ADMIN : Activer/Désactiver un événement
     */
    public function toggleEvent($id)
    {
        $event = VoteEvent::findOrFail($id);
        $event->is_active = !$event->is_active;
        $event->save();

        return redirect()->route('admin.vote-events.index')
            ->with('success', 'Statut de l\'événement modifié.');
    }

    /**
     * ADMIN : Afficher le QR Code pour un événement
     */
    public function showQrCode($id)
    {
        $event = VoteEvent::findOrFail($id);
        
        // Générer l'URL de validation du QR
        $qrUrl = route('vote-jour-j.validate-qr') . '?qr_secret=' . $event->qr_secret;

        return view('admin.vote-events.qr-code', compact('event', 'qrUrl'));
    }
}
