<?php

namespace App\Http\Controllers;

use App\Helpers\GeoHelper;
use App\Models\Projet;
use App\Models\Secteur;
use App\Models\Vote;
use App\Models\VoteEvent;
use App\Models\VoteJourJ;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use App\Http\Controllers\OrangeSmsController;

class VoteJourJController extends Controller
{
    /**
     * Affiche la page de vote Jour J
     * (similaire aux pages de vote normales, mais dédiée à l’événement physique)
     */
    public function show(Request $request)
    {
        $event = $this->getActiveEvent();

        $profileType = $request->query('profile_type'); // 'student' | 'startup' | 'other' | null
        $search      = trim($request->query('search', ''));

        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->where('is_finaliste', 1)
            ->pluck('projet_id');

        $secteurQuery = Secteur::query();

        $secteurQuery->whereHas('projets', function ($projetQuery) use ($preselectedProjectIds, $profileType) {
            $projetQuery->whereIn('id', $preselectedProjectIds);

            if (in_array($profileType, ['student', 'startup', 'other'])) {
                $projetQuery->whereHas('submission', function ($submissionQuery) use ($profileType) {
                    $submissionQuery->where('profile_type', $profileType);
                });
            }
        });

        if ($search !== '') {
            $secteurQuery->where(function ($q) use ($search, $preselectedProjectIds, $profileType) {
                $q->where('nom', 'like', '%' . $search . '%')
                    ->orWhereHas('projets', function ($subQuery) use ($search, $preselectedProjectIds, $profileType) {
                        $subQuery->whereIn('id', $preselectedProjectIds)
                            ->when(
                                in_array($profileType, ['student', 'startup', 'other']),
                                function ($qq) use ($profileType) {
                                    $qq->whereHas('submission', fn($s) => $s->where('profile_type', $profileType));
                                }
                            )
                            ->where(function ($subSubQuery) use ($search) {
                                $subSubQuery->where('nom_projet', 'like', '%' . $search . '%')
                                    ->orWhere('nom_equipe', 'like', '%' . $search . '%');
                            });
                    });
            });
        }

        $secteurQuery->with(['projets' => function ($projetQuery) use ($preselectedProjectIds, $profileType, $search) {
            $projetQuery
                ->whereIn('projets.id', $preselectedProjectIds)
                ->with(['submission', 'listePreselectionne'])
                ->when(
                    in_array($profileType, ['student', 'startup', 'other']),
                    function ($qq) use ($profileType) {
                        $qq->whereHas('submission', fn($s) => $s->where('profile_type', $profileType));
                    }
                )
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
     * Compat REST : si jamais un form POST /vote-jour-j
     * on délègue à la logique principale OTP + géoloc.
     *
     * Ce store NE TOUCHE PAS vote_publics.
     */
    public function projectData($id)
    {
        $projet = Projet::with(['submission', 'listePreselectionne'])->findOrFail($id);
        return response()->json($projet);
    }

   public function store(Request $request)
    {
        if ($request->filled('telephone') && $request->filled('projet_id')) {
            $normalized = $this->normalizePhone($request->input('telephone'));
            if (! $normalized) {
                return response()->json(['success' => false,'message' => 'Numéro de téléphone invalide.'], 422);
            }

            $request->session()->put('otp_data_jour_j', [
                'telephone'  => $normalized,
                'projet_id'  => (int) $request->input('projet_id'),
                'created_at' => now(),
            ]);
        }

        return $this->verifierOtp($request);
    }

    /**
     * ADMIN : Formulaire de création d'événement Jour J
     */
    public function createEvent()
    {
        return view('admin.vote-events.create');
    }

    /**
     * ADMIN : Liste / gestion des événements Jour J + classement des projets
     */
    public function indexEvents()
    {
        $events = VoteEvent::with('voteJourJ.vote.projet')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                $successfulVotes = $event->voteJourJ()
                    ->where('validation_status', 'success')
                    ->with('vote.projet')
                    ->get();

                $ranking = $successfulVotes
                    ->groupBy('vote.projet_id')
                    ->map(function ($votes) {
                        return [
                            'projet'     => $votes->first()->vote->projet,
                            'vote_count' => $votes->count(),
                        ];
                    })
                    ->sortByDesc('vote_count')
                    ->values();

                return [
                    'event'       => $event,
                    'total_votes' => $successfulVotes->count(),
                    'ranking'     => $ranking,
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
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'rayon_metres' => 'required|integer|min:10|max:1000',
            'date_debut'   => 'required|date_format:Y-m-d\TH:i',
            'date_fin'     => 'required|date_format:Y-m-d\TH:i|after:date_debut',
        ]);

        $qrSecret = bin2hex(random_bytes(32));

        VoteEvent::create([
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'rayon_metres' => $request->rayon_metres,
            'qr_secret'    => $qrSecret,
            'is_active'    => true,
            'date_debut'   => Carbon::createFromFormat('Y-m-d\TH:i', $request->date_debut),
            'date_fin'     => Carbon::createFromFormat('Y-m-d\TH:i', $request->date_fin),
        ]);

        return redirect()->route('admin.vote-events.index')
            ->with('success', '✅ Événement créé avec succès ! Secret QR: ' . substr($qrSecret, 0, 8) . '...');
    }
    /**
     * ADMIN : Activer / désactiver un événement Jour J
     */
    public function toggleEvent($id)
    {
        $event = VoteEvent::findOrFail($id);
        $event->is_active = ! $event->is_active;
        $event->save();

        return redirect()->route('admin.vote-events.index')
            ->with('success', 'Statut de l\'événement modifié.');
    }

    /**
     * ADMIN : Afficher le QR Code (URL d’accès au vote Jour J)
     */
    public function showQrCode($id)
    {
        $event = VoteEvent::findOrFail($id);

        // URL de la page de vote Jour J
        $qrUrl = route('vote-jour-j.show');

        return view('admin.vote-events.qr-code', compact('event', 'qrUrl'));
    }

    /**
     * ENVOI OTP Jour J
     * ----------------
     * - Normalise le téléphone (E.164)
     * - Vérifie uniquement dans la table `votes` (Jour J)
     * - Génère un OTP stocké dans `otp_codes`
     * - Envoie le SMS via OrangeSmsController::sendSmsInternal()
     * - Sauvegarde (téléphone, projet) en session pour la suite
     */
    public function envoyerOtp(Request $request)
    {
        $validated = $request->validate([
            'projet_id' => 'required|integer|exists:projets,id',
            'telephone' => 'required|string|min:6|max:20',
        ]);

        // Événement actif obligatoire pour Jour J
        $event = $this->getActiveEvent();
        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun événement de vote Jour J n\'est actif pour le moment.',
            ], 403);
        }

        $normalizedPhone = $this->normalizePhone($validated['telephone']);
        if (! $normalizedPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Numéro de téléphone invalide.',
            ], 422);
        }

        $projetId = (int) $validated['projet_id'];

        // Option : un seul vote par téléphone & projet
        $alreadyVoted = Vote::where('telephone', $normalizedPhone)
            ->where('projet_id', $projetId)
            ->exists();

        if ($alreadyVoted) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà voté pour ce projet.',
            ], 409);
        }

        // Rate limiting simple (IP + téléphone) pour éviter les abus
        $ip = $request->ip();

        $ipKey   = 'jourj_otp_ip:' . $ip;
        $ipCount = Cache::get($ipKey, 0) + 1;
        Cache::put($ipKey, $ipCount, now()->addMinutes(10));

        if ($ipCount > 30) { // 30 OTP / 10 min / IP
            Log::warning('Rate limit OTP Jour J (IP)', ['ip' => $ip, 'count' => $ipCount]);

            return response()->json([
                'success' => false,
                'message' => 'Trop de demandes depuis cette adresse IP. Réessayez plus tard.',
            ], 429);
        }

        $phoneKey   = 'jourj_otp_phone:' . $normalizedPhone;
        $phoneCount = Cache::get($phoneKey, 0) + 1;
        Cache::put($phoneKey, $phoneCount, now()->addHour());

        if ($phoneCount > 5) { // 5 OTP / heure / téléphone
            Log::warning('Rate limit OTP Jour J (phone)', [
                'phone_last4' => substr(preg_replace('/\D+/', '', $normalizedPhone), -4),
                'count'       => $phoneCount,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Trop de demandes de code pour ce numéro. Réessayez plus tard.',
            ], 429);
        }

        // Générer OTP
        $otp  = random_int(100000, 999999);
        $hash = Hash::make((string) $otp);

        try {
            // On peut nettoyer d’anciens OTP pour ce couple (phone, projet)
            DB::table('otp_codes')
                ->where('phone', $normalizedPhone)
                ->where('projet_id', $projetId)
                ->delete();

            DB::table('otp_codes')->insert([
                'phone'       => $normalizedPhone,
                'projet_id'   => $projetId,
                'code_hash'   => $hash,
                'expires_at'  => now()->addMinutes(5),
                'attempts'    => 0,
                'consumed_at' => null,
                'ip_address'  => $ip,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (QueryException $e) {
            Log::warning('Erreur insertion OTP Jour J', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de la génération du code.',
            ], 500);
        }

        // Envoi SMS via Orange
        $smsController = new OrangeSmsController();
        $message       = "Votre code de vote Jour J est : {$otp}";

        $smsResult = $smsController->sendSmsInternal($normalizedPhone, $message);

        if (! $smsResult['ok']) {
            Log::error('Erreur envoi SMS OTP Jour J', [
                'status' => $smsResult['status'],
                'body'   => $smsResult['body'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du SMS. Veuillez réessayer.',
            ], 500);
        }

        // Stocker en session pour la phase suivante (OTP + GPS)
        $request->session()->put('otp_data_jour_j', [
            'telephone'  => $normalizedPhone,
            'projet_id'  => $projetId,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Code OTP envoyé. Veuillez le saisir et autoriser la géolocalisation pour valider votre vote.',
        ]);
    }

    /**
     * VÉRIFICATION OTP + GÉOLOCALISATION Jour J
     * ----------------------------------------
     * - Lit téléphone & projet dans la session (envoyerOtp)
     *   OU optionnellement depuis la requête (compat).
     * - Vérifie le code OTP dans otp_codes (sans toucher vote_publics)
     * - Valide la position GPS par rapport à l’événement Jour J
     * - Enregistre le vote dans `votes`
     * - Logue le détail dans `vote_jour_j`
     */
    public function verifierOtp(Request $request)
    {
        $validated = $request->validate([
            'code_otp'  => 'required|string|size:6',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Récupérer les données OTP de la session (flow normal)
        $otpData = $request->session()->get('otp_data_jour_j');

        // Compat : si la session est vide mais que téléphone/projet arrivent dans la requête,
        // on les utilise (ex: form direct POST /store).
        if (! $otpData && $request->filled('telephone') && $request->filled('projet_id')) {
            $normalized = $this->normalizePhone($request->input('telephone'));

            if (! $normalized) {
                return response()->json([
                    'success' => false,
                    'message' => 'Numéro de téléphone invalide.',
                ], 422);
            }

            $otpData = [
                'telephone'  => $normalized,
                'projet_id'  => (int) $request->input('projet_id'),
                'created_at' => now(),
            ];
        }

        if (! $otpData) {
            return response()->json([
                'success' => false,
                'message' => 'Session OTP expirée ou invalide. Merci de recommencer.',
            ], 401);
        }

        $telephone = $otpData['telephone'];
        $projetId  = (int) $otpData['projet_id'];

        // Événement actif obligatoire
        $event = $this->getActiveEvent();
        if (! $event) {
            $request->session()->forget('otp_data_jour_j');

            return response()->json([
                'success' => false,
                'message' => 'Aucun événement de vote Jour J n\'est actif.',
            ], 403);
        }

        // Vérification de l’OTP pour ce couple (phone, projet)
        if (! $this->verifyOtpCode($telephone, $projetId, $validated['code_otp'])) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide ou expiré.',
            ], 422);
        }

        // Vérification de la géolocalisation (distance en mètres)
        $distance = GeoHelper::haversineDistance(
            $event->latitude,
            $event->longitude,
            $validated['latitude'],
            $validated['longitude']
        );

        if ($distance > $event->rayon_metres) {
            // Loguer l’essai hors zone
            VoteJourJ::create([
                'vote_id'            => null,
                'vote_event_id'      => $event->id,
                'latitude_user'      => $validated['latitude'],
                'longitude_user'     => $validated['longitude'],
                'distance_metres'    => $distance,
                'qr_token_used'      => null,
                'qr_token_expires_at'=> null,
                'validation_status'  => 'outside_zone',
            ]);

            $request->session()->forget('otp_data_jour_j');

            return response()->json([
                'success' => false,
                'message' => "Vous devez être dans le périmètre de la salle pour voter. Distance: {$distance} m (maximum: {$event->rayon_metres} m).",
            ], 403);
        }

        // Vérifier si ce téléphone a déjà voté pour ce projet dans la table `votes`
        $existingVote = Vote::where('telephone', $telephone)
            ->where('projet_id', $projetId)
            ->first();

        if ($existingVote) {
            $request->session()->forget('otp_data_jour_j');

            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà voté pour ce projet.',
            ], 422);
        }

        // Tout est OK : on enregistre le vote + le log Jour J
        try {
            DB::transaction(function () use (
                $projetId,
                $telephone,
                $event,
                $distance,
                $validated,
                $request
            ) {
                $vote = Vote::create([
                    'projet_id'  => $projetId,
                    'telephone'  => $telephone,
                    'ip_address' => $request->ip(),
                    'user_agent' => substr($request->userAgent() ?? 'unknown', 0, 1000),
                ]);

                VoteJourJ::create([
                    'vote_id'            => $vote->id,
                    'vote_event_id'      => $event->id,
                    'latitude_user'      => $validated['latitude'],
                    'longitude_user'     => $validated['longitude'],
                    'distance_metres'    => $distance,
                    'qr_token_used'      => null,
                    'qr_token_expires_at'=> null,
                    'validation_status'  => 'success',
                ]);
            });
        } catch (QueryException $e) {
            Log::warning('Erreur enregistrement vote Jour J', [
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            $request->session()->forget('otp_data_jour_j');

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de l\'enregistrement du vote.',
            ], 500);
        }

        $request->session()->forget('otp_data_jour_j');

        return response()->json([
            'success' => true,
            'message' => 'Vote enregistré avec succès ! La délibération aura lieu dans quelques instants.',
        ]);
    }

    /* ============================================================
     | Helpers privés
     * ============================================================*/

    /**
     * Récupère l’événement actif (date + is_active).
     */
    private function getActiveEvent(): ?VoteEvent
    {
        return VoteEvent::where('is_active', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->latest('created_at')
            ->first();
    }

    /**
     * Normalise un numéro au format E.164 pour le Sénégal
     * (via libphonenumber, retour null si invalide).
     */
    private function normalizePhone(string $rawPhone): ?string
    {
        $util = PhoneNumberUtil::getInstance();
        try {
            $phone = $util->parse($rawPhone, 'SN');
            if (! $util->isValidNumber($phone)) return null;
            return $util->format($phone, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            Log::warning('Erreur parsing téléphone Jour J', ['raw' => $rawPhone,'message' => $e->getMessage()]);
            return null;
        }
    }
    /**
     * Vérifie un OTP pour un couple (téléphone, projet)
     * dans la table otp_codes, sans toucher vote_publics.
     */
    private function verifyOtpCode(string $telephone, int $projetId, string $code): bool
    {
        $otpRow = DB::table('otp_codes')
            ->where('phone', $telephone)
            ->where('projet_id', $projetId)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if (! $otpRow) {
            return false;
        }

        // Expiration
        if (isset($otpRow->expires_at) && now()->greaterThan($otpRow->expires_at)) {
            return false;
        }

        // Limite d’essais (facultatif)
        if (isset($otpRow->attempts) && $otpRow->attempts >= 5) {
            return false;
        }

        if (! Hash::check($code, $otpRow->code_hash)) {
            DB::table('otp_codes')
                ->where('id', $otpRow->id)
                ->update([
                    'attempts'   => ($otpRow->attempts ?? 0) + 1,
                    'updated_at' => now(),
                ]);

            return false;
        }

        // OTP valide : on marque comme consommé
        DB::table('otp_codes')
            ->where('id', $otpRow->id)
            ->update([
                'consumed_at' => now(),
                'updated_at'  => now(),
            ]);

        return true;
    }

    /**
     * ADMIN : Supprime un événement Vote Jour J
     */

     public function destroyEvent($id)
     {
         $event = VoteEvent::findOrFail($id);

         try {
             VoteJourJ::where('vote_event_id', $event->id)->delete();
             $event->delete();

             return redirect()
                 ->route('admin.vote-events.index')
                 ->with('success', "L'événement #{$id} a été supprimé avec succès.");
         } catch (\Exception $e) {
             Log::error('Erreur lors de la suppression d\'un événement', [
                 'event_id' => $id,
                 'error'    => $e->getMessage(),
             ]);

             return redirect()
                 ->route('admin.vote-events.index')
                 ->with('error', 'Une erreur est survenue lors de la suppression de l\'événement.');
         }
     }
}
