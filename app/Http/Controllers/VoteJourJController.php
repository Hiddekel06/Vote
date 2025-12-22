<?php

namespace App\Http\Controllers;

use App\Helpers\GeoHelper;
use App\Models\Projet;
use App\Models\Secteur;
use App\Models\Vote;
use App\Models\VoteEvent;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VoteJourJController extends Controller
{
    /**
     * Affiche la page de vote Jour J (finalistes uniquement)
     */
    public function show(Request $request)
    {
        $event = $this->getActiveEvent();

        $profileType = $request->query('profile_type'); // 'student' | 'startup' | 'other' | null
        $search      = trim((string) $request->query('search', ''));

        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->where('is_finaliste', 1)
            ->pluck('projet_id');

        $secteurQuery = Secteur::query();

        $secteurQuery->whereHas('projets', function ($projetQuery) use ($preselectedProjectIds, $profileType) {
            $projetQuery->whereIn('id', $preselectedProjectIds);

            if (in_array($profileType, ['student', 'startup', 'other'], true)) {
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
                                in_array($profileType, ['student', 'startup', 'other'], true),
                                function ($qq) use ($profileType) {
                                    $qq->whereHas('submission', fn ($s) => $s->where('profile_type', $profileType));
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
                    in_array($profileType, ['student', 'startup', 'other'], true),
                    function ($qq) use ($profileType) {
                        $qq->whereHas('submission', fn ($s) => $s->where('profile_type', $profileType));
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

        $isVoteActive     = (bool) $event;
        $inactiveMessage  = "Le vote Jour J n'est pas ouvert pour le moment.";

        return view('vote-jour-j', compact('secteurs', 'event', 'isVoteActive', 'inactiveMessage'));
    }

    /**
     * API: infos projet
     */
    public function projectData($id)
    {
        $projet = Projet::with(['submission', 'listePreselectionne'])->findOrFail($id);
        return response()->json($projet);
    }

    /**
     * Compat REST : si POST /vote-jour-j
     */
    public function store(Request $request)
    {
        if ($request->filled('telephone') && $request->filled('projet_id')) {
            $normalized = $this->normalizePhone((string) $request->input('telephone'));
            if (! $normalized) {
                return response()->json(['success' => false, 'message' => 'Numéro de téléphone invalide.'], 422);
            }

            $request->session()->put('otp_data_jour_j', [
                'telephone'  => $normalized,
                'projet_id'  => (int) $request->input('projet_id'),
                'created_at' => now(),
            ]);
        }

        return $this->verifierOtp($request);
    }

    /* ========================= ADMIN EVENTS ========================= */

    public function createEvent()
    {
        return view('admin.vote-events.create');
    }

    public function indexEvents()
    {
        $events = VoteEvent::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {

                $successfulVotes = $event->votes()
                    ->where('validation_status', 'success')
                    ->with('projet')
                    ->get();

                $ranking = $successfulVotes
                    ->groupBy('projet_id')
                    ->map(function ($votes) {
                        return [
                            'projet'     => $votes->first()->projet,
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

    public function toggleEvent($id)
    {
        $event = VoteEvent::findOrFail($id);
        $event->is_active = ! $event->is_active;
        $event->save();

        return redirect()->route('admin.vote-events.index')
            ->with('success', 'Statut de l\'événement modifié.');
    }

    public function showQrCode($id)
    {
        $event = VoteEvent::findOrFail($id);
        $qrUrl = route('vote-jour-j.show');
        return view('admin.vote-events.qr-code', compact('event', 'qrUrl'));
    }

    /* ========================= OTP FLOW ========================= */

    public function envoyerOtp(Request $request)
    {
        $validated = $request->validate([
            'projet_id'  => 'required|integer|exists:projets,id',
            'telephone'  => 'required|string|min:6|max:30',
            'nom_votant' => 'nullable|string|max:120',
        ]);

        // Événement actif obligatoire
        $event = $this->getActiveEvent();
        if (! $event) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun événement de vote Jour J n\'est actif pour le moment.',
            ], 403);
        }

        // Normalisation
        $normalizedPhone = $this->normalizePhone($validated['telephone']);
        if (! $normalizedPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Numéro de téléphone invalide.',
            ], 422);
        }

        $projetId = (int) $validated['projet_id'];

        // Un seul vote par téléphone & projet (dans l'event)
        $alreadyVoted = Vote::where('telephone', $normalizedPhone)
            ->where('projet_id', $projetId)
            ->where('vote_event_id', $event->id)
            ->where('validation_status', 'success')
            ->exists();

        if ($alreadyVoted) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà voté pour ce projet.',
            ], 409);
        }

        // Rate limiting (IP + téléphone)
        $ip = (string) $request->ip();

        $ipKey   = 'jourj_otp_ip:' . $ip;
        $ipCount = (int) Cache::get($ipKey, 0) + 1;
        Cache::put($ipKey, $ipCount, now()->addMinutes(10));
        if ($ipCount > 30) {
            Log::warning('Rate limit OTP Jour J (IP)', ['ip' => $ip, 'count' => $ipCount]);
            return response()->json([
                'success' => false,
                'message' => 'Trop de demandes depuis cette adresse IP. Réessayez plus tard.',
            ], 429);
        }

        $phoneKey   = 'jourj_otp_phone:' . $normalizedPhone;
        $phoneCount = (int) Cache::get($phoneKey, 0) + 1;
        Cache::put($phoneKey, $phoneCount, now()->addHour());
        if ($phoneCount > 5) {
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
            // supprime l'ancien OTP de ce tel + projet
            DB::table('otp_codes')
                ->where('phone', $normalizedPhone)
                ->where('projet_id', $projetId)
                ->delete();

            // ✅ on met tout ce qui peut poser problème à NULL si pas obligatoire
            DB::table('otp_codes')->insert([
                'phone'       => $normalizedPhone,
                'projet_id'   => $projetId,
                'code_hash'   => $hash,
                'expires_at'  => now()->addMinutes(5),
                'attempts'    => 0,
                'consumed_at' => null,

                // ces champs peuvent casser si schéma différent => NULL
                'ip_address'  => $ip ?? null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (QueryException $e) {
            Log::error('Erreur insertion OTP Jour J', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de la génération du code.',
            ], 500);
        }

        // Envoi SMS via Orange
        $message = "Votre code de vote pour la grande finale est : {$otp}";

        try {
            /** @var \App\Http\Controllers\OrangeSmsController $smsController */
            $smsController = app(\App\Http\Controllers\OrangeSmsController::class);
            $smsResult = $smsController->sendSmsInternal($normalizedPhone, $message);
        } catch (\Throwable $e) {
            Log::error('Exception envoi SMS OTP Jour J', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du SMS. Veuillez réessayer.',
            ], 500);
        }

        if (!is_array($smsResult) || empty($smsResult['ok'])) {
            Log::error('Erreur envoi SMS OTP Jour J', [
                'status' => $smsResult['status'] ?? null,
                'body'   => $smsResult['body'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du SMS. Veuillez réessayer.',
            ], 500);
        }

        // Stocker en session (utile mais pas obligatoire grâce au fallback)
        $request->session()->put('otp_data_jour_j', [
            'telephone'  => $normalizedPhone,
            'projet_id'  => $projetId,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Code OTP envoyé. Veuillez le saisir pour valider votre vote.',
        ]);
    }

    public function verifierOtp(Request $request)
    {
        $validated = $request->validate([
            'code_otp'  => ['required', 'regex:/^\d{6}$/'],
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',

            // ✅ fallback si session perdue (IMPORTANT)
            'telephone' => 'nullable|string|min:6|max:30',
            'projet_id' => 'nullable|integer|exists:projets,id',
        ]);

        $otpData = $request->session()->get('otp_data_jour_j');

        // ✅ si session vide, on reconstruit depuis le front
        if (! $otpData && $request->filled('telephone') && $request->filled('projet_id')) {
            $normalized = $this->normalizePhone((string) $request->input('telephone'));
            if (! $normalized) {
                return response()->json(['success' => false, 'message' => 'Numéro de téléphone invalide.'], 422);
            }

            $otpData = [
                'telephone' => $normalized,
                'projet_id' => (int) $request->input('projet_id'),
                'created_at'=> now(),
            ];
        }

        if (! $otpData) {
            return response()->json([
                'success' => false,
                'message' => 'Session OTP expirée ou invalide. Merci de recommencer.',
            ], 401);
        }

        $telephone = (string) $otpData['telephone'];
        $projetId  = (int) $otpData['projet_id'];

        // Événement actif obligatoire
        $event = $this->getActiveEvent();
        if (! $event) {
            $request->session()->forget('otp_data_jour_j');
            return response()->json([
                'success' => false,
                'message' => 'Aucun événement actif.',
            ], 403);
        }

        // ✅ OTP check (sécurisé)
        if (! $this->verifyOtpCode($telephone, $projetId, (string) $validated['code_otp'])) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide ou expiré.',
            ], 422);
        }

        // Vérification géoloc
        $distance = GeoHelper::haversineDistance(
            $event->latitude,
            $event->longitude,
            $validated['latitude'],
            $validated['longitude']
        );

        if ($distance > $event->rayon_metres) {

            // Log essai (champs sensibles => NULL si pas sûrs)
            try {
                Vote::create([
                    'projet_id'         => $projetId,
                    'telephone'         => $telephone,
                    'ip_address'        => $request->ip() ?? null,
                    'user_agent'        => $request->userAgent() ? substr((string)$request->userAgent(), 0, 1000) : null,

                    'vote_event_id'     => $event->id,
                    'latitude_user'     => $validated['latitude'] ?? null,
                    'longitude_user'    => $validated['longitude'] ?? null,
                    'distance_metres'   => $distance ?? null,
                    'validation_status' => 'outside_zone',
                ]);
            } catch (\Throwable $e) {
                Log::warning('Vote outside_zone non loggé (colonnes?)', ['err' => $e->getMessage()]);
            }

            $request->session()->forget('otp_data_jour_j');

            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas dans la zone de vote autorisée.",
            ], 403);
        }

        // Double sécurité : déjà voté ?
        $existingVote = Vote::where('telephone', $telephone)
            ->where('projet_id', $projetId)
            ->where('vote_event_id', $event->id)
            ->where('validation_status', 'success')
            ->first();

        if ($existingVote) {
            $request->session()->forget('otp_data_jour_j');
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà voté pour ce projet.',
            ], 409);
        }

        // Save vote
        try {
            DB::transaction(function () use ($projetId, $telephone, $event, $distance, $validated, $request) {
                Vote::create([
                    'projet_id'         => $projetId,
                    'telephone'         => $telephone,

                    // ✅ si colonnes existent pas / ou pas fiables => NULL ok
                    'ip_address'        => $request->ip() ?? null,
                    'user_agent'        => $request->userAgent() ? substr((string)$request->userAgent(), 0, 1000) : null,

                    'vote_event_id'     => $event->id,
                    'latitude_user'     => $validated['latitude'] ?? null,
                    'longitude_user'    => $validated['longitude'] ?? null,
                    'distance_metres'   => $distance ?? null,
                    'validation_status' => 'success',
                ]);
            });
        } catch (QueryException $e) {
            Log::error('Erreur enregistrement vote Jour J', [
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

    /* ========================= HELPERS ========================= */

    private function getActiveEvent(): ?VoteEvent
    {
        return VoteEvent::where('is_active', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->latest('created_at')
            ->first();
    }

    /**
     * Normalise en E.164 Sénégal sans libphonenumber:
     * - accepte: 77 123 45 67 / +221771234567 / 221771234567
     * - renvoie: +221771234567
     */
    private function normalizePhone(string $rawPhone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $rawPhone);

        if (! $digits) {
            return null;
        }

        if (strlen($digits) < 9) {
            return null;
        }

        $national = substr($digits, -9);

        if (!preg_match('/^\d{9}$/', $national)) {
            return null;
        }

        return '+221' . $national;
    }

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

        // ✅ expiry robuste
        if (!empty($otpRow->expires_at)) {
            try {
                if (now()->greaterThan(Carbon::parse($otpRow->expires_at))) {
                    return false;
                }
            } catch (\Throwable $e) {
                // si format foireux, on considère expiré par sécurité
                return false;
            }
        }

        if (isset($otpRow->attempts) && (int)$otpRow->attempts >= 5) {
            return false;
        }

        if (! Hash::check($code, $otpRow->code_hash)) {
            DB::table('otp_codes')
                ->where('id', $otpRow->id)
                ->update([
                    'attempts'   => ((int)($otpRow->attempts ?? 0)) + 1,
                    'updated_at' => now(),
                ]);

            return false;
        }

        // Consommer
        DB::table('otp_codes')
            ->where('id', $otpRow->id)
            ->update([
                'consumed_at' => now(),
                'updated_at'  => now(),
            ]);

        return true;
    }

    public function destroyEvent($id)
    {
        $event = VoteEvent::findOrFail($id);

        try {
            Vote::where('vote_event_id', $event->id)->delete();
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
