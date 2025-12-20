<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\OrangeSmsController;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\View\View;
use App\Models\Vote;
use App\Models\VotePublic;
use App\Models\Configuration;
use App\Models\Secteur;
use App\Models\Projet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;   // pour le rate limiting


class VoteController extends Controller
{
    public function choixCategorie(): View
    {
        $categories = collect([
            (object) ['nom' => 'Étudiant', 'slug' => 'student'],
            (object) ['nom' => 'Startup', 'slug' => 'startup'],
            (object) ['nom' => 'Porteurs de projet', 'slug' => 'other'],
        ]);

        return view('vote', compact('categories'));
    }

    public function index(Request $request, string $profileType): View
    {
        $categorieNom = match ($profileType) {
            'student' => 'Étudiant',
            'startup' => 'Startup',
            'other'   => 'Porteurs de projet',
            default   => 'Inconnue',
        };

        $categorie = (object) ['nom' => $categorieNom, 'slug' => $profileType];

        $search = trim($request->input('search', ''));

        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->where('is_finaliste', 1)
            ->select('projet_id');

        $query = Secteur::query();

        // Always filter to only sectors that have projects matching the profile
        $query->whereHas('projets', function ($projetQuery) use ($profileType, $preselectedProjectIds) {
            $projetQuery
                ->whereHas('submission', function ($submissionQuery) use ($profileType) {
                    $submissionQuery->where('profile_type', $profileType);
                })
                ->whereIn('id', $preselectedProjectIds);
        });

        // Vérifier si la recherche correspond exactement à un projet ou une équipe
        $hasExact = false;
        if ($search !== '') {
            $hasExact = Projet::whereIn('id', $preselectedProjectIds)
                ->whereHas('submission', fn($q) => $q->where('profile_type', $profileType))
                ->where(function ($q) use ($search) {
                    $q->where('nom_projet', $search)
                      ->orWhere('nom_equipe', $search);
                })->exists();

            // Afficher un secteur si son nom correspond, ou s'il contient des projets correspondant (exact si possible)
            $query->where(function ($q) use ($search, $preselectedProjectIds, $hasExact) {
                $q->where('nom', 'like', $search . '%')
                    ->orWhereHas('projets', function ($subQuery) use ($search, $preselectedProjectIds, $hasExact) {
                        $subQuery->whereIn('id', $preselectedProjectIds)
                            ->where(function ($subSubQuery) use ($search, $hasExact) {
                                if ($hasExact) {
                                    $subSubQuery->where('nom_projet', $search)
                                                ->orWhere('nom_equipe', $search);
                                } else {
                                    $subSubQuery->where('nom_projet', 'like', $search . '%')
                                                ->orWhere('nom_equipe', 'like', $search . '%');
                                }
                            });
                    });
            });
        }

        $query->with(['projets' => function ($projetQuery) use ($search, $profileType, $preselectedProjectIds, $hasExact) {
            $projetQuery
                ->leftJoin('liste_preselectionnes', 'projets.id', '=', 'liste_preselectionnes.projet_id')
                ->select('projets.*', 'liste_preselectionnes.video_demonstration')
                ->with('submission', 'listePreselectionne')
                ->whereHas('submission', fn($q) => $q->where('profile_type', $profileType))
                ->whereIn('projets.id', $preselectedProjectIds)
                ->when($search, function ($q) use ($search, $hasExact) {
                    if ($hasExact) {
                        $q->where(function ($qq) use ($search) {
                            $qq->where('nom_projet', $search)
                               ->orWhere('nom_equipe', $search);
                        });
                    } else {
                        $q->where(function ($qq) use ($search) {
                            $qq->where('nom_projet', 'like', $search . '%')
                               ->orWhere('nom_equipe', 'like', $search . '%');
                        });
                    }
                })
                ->orderBy('nom_projet');
        }]);

        $secteurs = $query->orderBy('nom')->get();

        $countriesData = json_decode(File::get(public_path('data/countries.json')), true);
        $countries = array_map(function ($country) {
            $country['flag'] = $this->isoToFlag($country['code']);
            return $country;
        }, $countriesData);
        usort($countries, fn($a, $b) => $a['name'] <=> $b['name']);

        $allCategories = collect([
            (object) ['nom' => 'Étudiant', 'slug' => 'student'],
            (object) ['nom' => 'Startup', 'slug' => 'startup'],
            (object) ['nom' => 'Porteurs de projet', 'slug' => 'other'],
        ]);

        $voteStatusDetails = $this->getVoteStatusDetails();

        $perPage = (int) $request->query('per_page', 5);

        $projetsQuery = Projet::with(['secteur', 'listePreselectionne'])
            ->whereHas('submission', fn($q) => $q->where('profile_type', $profileType))
            ->whereIn('id', $preselectedProjectIds)
            ->when($search, function ($q) use ($search, $hasExact) {
                if ($hasExact) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('nom_projet', $search)
                           ->orWhere('nom_equipe', $search);
                    });
                } else {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('nom_projet', 'like', $search . '%')
                           ->orWhere('nom_equipe', 'like', $search . '%');
                    });
                }
            })
            ->orderBy('nom_projet');

        $projets = $projetsQuery->paginate($perPage)->withQueryString();

        return view('vote_secteurs', compact('secteurs', 'projets', 'countries', 'voteStatusDetails', 'categorie', 'allCategories'));
    }

    public function rechercheAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $search = $request->input('search', '');

        $preselectedProjectIds = DB::table('liste_preselectionnes')
            ->where('is_finaliste', 1)
            ->select('projet_id');

        $query = Secteur::query();

        if ($search) {
            $query->where(function ($q) use ($search, $preselectedProjectIds) {
                $q->where('nom', 'like', '%' . $search . '%')
                    ->orWhereHas('projets', function ($subQuery) use ($search, $preselectedProjectIds) {
                        $subQuery
                            ->whereIn('id', $preselectedProjectIds)
                            ->where(function ($subSubQuery) use ($search) {
                                $subSubQuery->where('nom_projet', 'like', '%' . $search . '%')
                                    ->orWhere('nom_equipe', 'like', '%' . $search . '%');
                            });
                    });
            });
        } else {
            $query->whereHas('projets', function ($subQuery) use ($preselectedProjectIds) {
                $subQuery->whereIn('id', $preselectedProjectIds);
            });
        }

        $query->with(['projets' => function ($projetQuery) use ($search, $preselectedProjectIds) {
            $projetQuery
                ->leftJoin('liste_preselectionnes', 'projets.id', '=', 'liste_preselectionnes.projet_id')
                ->select('projets.*', 'liste_preselectionnes.video_demonstration')
                ->whereIn('projets.id', $preselectedProjectIds);

            if ($search) {
                $projetQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('nom_projet', 'like', '%' . $search . '%')
                        ->orWhere('nom_equipe', 'like', '%' . $search . '%');
                });
            }

            $projetQuery->orderBy('nom_projet');
        }])->orderBy('nom');

        $secteurs = $query->get()
            ->filter(fn($secteur) => $secteur->projets->isNotEmpty())
            ->values();

        return response()->json($secteurs);
    }

    public function envoyerOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate(
            [
                'projet_id'        => 'required|exists:projets,id',
                'country_code'     => 'required|string',
                'telephone_display'=> 'required|string',
                'nom_votant'       => 'nullable|string|max:255',
                'recaptcha_token'  => config('services.recaptcha.enabled', false) ? 'required|string' : 'nullable|string',
            ],
            [
                'projet_id.required'         => 'Le projet est obligatoire.',
                'projet_id.exists'           => 'Le projet sélectionné est invalide.',
                'country_code.required'      => 'Le code pays est obligatoire.',
                'country_code.string'        => 'Le code pays est invalide.',
                'telephone_display.required' => 'Le numéro de téléphone est obligatoire.',
                'telephone_display.string'   => 'Le numéro de téléphone est invalide.',
                'nom_votant.string'          => 'Le nom saisi est invalide.',
                'nom_votant.max'             => 'Le nom ne doit pas dépasser 255 caractères.',
                'recaptcha_token.required'   => 'La vérification de sécurité est obligatoire.',
                'recaptcha_token.string'     => 'La vérification de sécurité est invalide.',
            ]
        );

        $this->checkVoteStatus();
                // 🔒 Rate limiting par IP (anti-bot brut)
                $ip = $request->ip();
                $ipKey = 'vote_otp_ip:' . $ip;
                $ipCount = Cache::get($ipKey, 0) + 1;
                // 10 minutes de fenêtre
                Cache::put($ipKey, $ipCount, now()->addMinutes(10));

                if ($ipCount > 10) { // > 10 OTP en 10 minutes = très suspect
                    Log::warning('Rate limit OTP par IP dépassé', [
                        'ip' => $ip,
                        'count' => $ipCount,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Trop de demandes de code ip enregistré',
                    ], 429);
                }


        $projetId   = $validated['projet_id'];
        $nomVotant  = $validated['nom_votant'] ?? null;
        $countryCode = $validated['country_code'];
        $telephoneDisplay = $validated['telephone_display'];

        $e164 = null;

        if (class_exists(PhoneNumberUtil::class)) {
            try {
                $phoneUtil = PhoneNumberUtil::getInstance();
                $digitsCountry = preg_replace('/\D+/', '', $countryCode);
                $digitsLocal = preg_replace('/\D+/', '', $telephoneDisplay);
                $raw = '+' . $digitsCountry . $digitsLocal;
                $proto = $phoneUtil->parse($raw, null);

                if (!$phoneUtil->isValidNumber($proto)) {
                    return response()->json(['success' => false, 'message' => 'Numéro de téléphone invalide.'], 422);
                }

                $e164 = $phoneUtil->format($proto, PhoneNumberFormat::E164);
            } catch (NumberParseException $e) {
                return response()->json(['success' => false, 'message' => 'Impossible d’analyser le numéro. Vérifiez le format.'], 422);
            } catch (\Throwable $e) {
                $e164 = null;
            }
        }

        if (!$e164) {
            $digitsCountry = preg_replace('/\D+/', '', $countryCode);
            $digitsLocal = preg_replace('/\D+/', '', $telephoneDisplay);
            if (empty($digitsCountry) || empty($digitsLocal)) {
                return response()->json(['success' => false, 'message' => 'Numéro de téléphone invalide.'], 422);
            }
            $e164 = '+' . $digitsCountry . $digitsLocal;
        }

        $telephone = $e164;

                $phoneKey   = 'vote_otp_phone:' . $telephone;
                $phoneCount = Cache::get($phoneKey, 0) + 1;
                Cache::put($phoneKey, $phoneCount, now()->addHour());

                if ($phoneCount > 5) {
                    Log::warning('Rate limit OTP par téléphone dépassé', [
                        'phone_last4' => substr(preg_replace('/\D+/', '', $telephone), -4),
                        'count'       => $phoneCount,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Trop de demandes de code pour ce numéro. Réessayez plus tard.',
                    ], 429);
                }


        try {
            $alreadyVoted = DB::table('vote_publics')
                ->where('telephone', $telephone)
                ->where('est_verifie', true)
                ->exists();

            if ($alreadyVoted) {
                return response()->json(['success' => false, 'message' => 'Vous avez déjà voté. Un seul vote est autorisé.'], 409);
            }
        } catch (\Throwable $e) {
            Log::warning('Erreur lors des vérifications pré-OTP', ['error' => $e->getMessage()]);
        }

        if (config('services.recaptcha.enabled', false)) {
            $recaptchaToken = $validated['recaptcha_token'];

            $response = Http::withoutVerifying()
                ->asForm()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => config('services.recaptcha.secret_key'),
                    'response' => $recaptchaToken,
                    'remoteip' => $request->ip(),
                ]);

            $body = $response->json();

            if (!isset($body['success']) || !$body['success'] || (isset($body['score']) && $body['score'] < 0.7)) {
                return response()->json(['success' => false, 'message' => 'La vérification de sécurité a échoué. Veuillez réessayer.'], 422);
            }
        }

        $otp = random_int(100000, 999999);

        $request->session()->put('otp_data', [
            'projet_id'   => $projetId,
            'telephone'   => $telephone,
            'nom_votant'  => $nomVotant,
            'otp'         => $otp,
            'expires_at'  => now()->addMinutes(10),
            'attempts'    => 0,
            // 🔒 on lie la session OTP à la machine
            'ip'          => $request->ip(),
            'user_agent'  => substr($request->userAgent() ?? 'unknown', 0, 1000),
        ]);


        // 3. Envoyer le code via Orange SMS (couche bas niveau)
try {
    $orangeSms = new OrangeSmsController();

    $message = "Votre code de vote GovAthon est : {$otp}";
    $result  = $orangeSms->sendSmsInternal($telephone, $message);

    if (! $result['ok']) {
        Log::error('Échec de l\'envoi de l\'OTP via Orange SMS', [
            'status' => $result['status'],
            'body'   => $result['body'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'envoi du code. Veuillez réessayer.',
        ], 500);
    }
} catch (\Exception $e) {
    Log::error('Erreur Orange SMS: ' . $e->getMessage());

    return response()->json([
        'success' => false,
        'message' => 'Erreur lors de l\'envoi du code. Veuillez vérifier le numéro de téléphone et réessayer.',
    ], 500);
}


        try {
            $digitsOnly = preg_replace('/\D+/', '', $telephone);
            $last4 = substr($digitsOnly, -4);
        } catch (\Throwable $e) {
            $last4 = null;
        }

        Log::info('OTP généré et envoyé (valeur non enregistrée)', ['phone_last4' => $last4]);

        return response()->json([
            'success' => true,
            'message' => 'Un code OTP a été envoyé.',
        ]);
    }

    public function verifierOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate(
            [
                'otp' => 'required|string|digits:6',
            ],
            [
                'otp.required' => 'Le code OTP est obligatoire.',
                'otp.string'   => 'Le code OTP est invalide.',
                'otp.digits'   => 'Le code OTP doit contenir exactement 6 chiffres.',
            ]
        );

        $this->checkVoteStatus();

        $submittedOtp = $validated['otp'];
        $otpData = $request->session()->get('otp_data');

        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'Session OTP expirée ou invalide. Veuillez recommencer le processus de vote.',
            ], 400);
        }
                // 🔒 Vérifier que l’IP et le user_agent n’ont pas changé entre l’envoi et la vérification
                if (($otpData['ip'] ?? null) !== $request->ip()) {
                    Log::warning('OTP IP mismatch', [
                        'stored_ip' => $otpData['ip'] ?? null,
                        'current_ip' => $request->ip(),
                    ]);

                    $request->session()->forget('otp_data');

                    return response()->json([
                        'success' => false,
                        'message' => 'Votre session de vote n’est plus valide. Merci de recommencer le processus de vote.',
                    ], 400);
                }


        if (now()->greaterThan($otpData['expires_at'])) {
            $request->session()->forget('otp_data');
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP a expiré. Veuillez demander un nouveau code.',
            ], 400);
        }

        $maxAttempts = 10;

        if (isset($otpData['attempts']) && $otpData['attempts'] >= $maxAttempts) {
            $request->session()->forget('otp_data');
            return response()->json([
                'success' => false,
                'message' => 'Trop de tentatives incorrectes. Veuillez recommencer le processus de vote.',
            ], 429);
        }

        if ($submittedOtp !== (string) $otpData['otp']) {
            $otpData['attempts'] = ($otpData['attempts'] ?? 0) + 1;
            $request->session()->put('otp_data', $otpData);

            return response()->json([
                'success' => false,
                'message' => 'Code OTP incorrect. Veuillez réessayer.',
            ], 401);
        }
        $phone    = $otpData['telephone'];
        $projetId = $otpData['projet_id'];

        try {
            $alreadyVoted = DB::table('vote_publics')
                ->where('telephone', $phone)
                ->where('est_verifie', true)
                ->exists();

            if ($alreadyVoted) {
                $request->session()->forget('otp_data');
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà voté. Un seul vote est autorisé.',
                ], 409);
            }

            // 🔒 infos techniques sur le vote
            $ip         = $request->ip();
            $userAgent  = substr($request->userAgent() ?? 'unknown', 0, 1000);
            $geoCountry = null;
            $geoCity    = null;

            try {
                if (function_exists('geoip')) {
                    $geo = geoip()->getLocation($ip);
                    $geoCountry = $geo->iso_code ?? $geo->country ?? null;
                    $geoCity    = $geo->city ?? null;
                }
            } catch (\Throwable $e) {
                Log::warning('GeoIP lookup failed (VoteController)', [
                    'ip'      => $ip,
                    'message' => $e->getMessage(),
                ]);
            }

            DB::transaction(function () use ($projetId, $phone, $ip, $userAgent, $geoCountry, $geoCity) {
                VotePublic::create([
                    'projet_id'   => $projetId,
                    'telephone'   => $phone,
                    'token'       => Str::uuid(),
                    'est_verifie' => true,
                    'ip_address'  => $ip,
                    'user_agent'  => $userAgent,
                    'geo_country' => $geoCountry,
                    'geo_city'    => $geoCity,
                ]);
            });

            $request->session()->forget('otp_data');

            return response()->json([
                'success' => true,
                'message' => 'Votre vote a été enregistré avec succès ! Merci de votre participation.',
            ]);

        } catch (QueryException $e) {
            $sqlState = $e->getCode();
            Log::warning('QueryException lors de la création du vote', [
                'code'    => $sqlState,
                'message' => $e->getMessage(),
            ]);

            $request->session()->forget('otp_data');

            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà voté. Un seul vote est autorisé.',
            ], 409);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'enregistrement du vote: ' . $e->getMessage());
            $request->session()->forget('otp_data');

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement de votre vote. Veuillez réessayer.',
            ], 500);
        }
    }

    public function afficherProjet($id)
    {
        $projet = Projet::with('secteur')->findOrFail($id);
        $secteurs = Secteur::with('projets')->get();

        $countriesData = json_decode(File::get(public_path('data/countries.json')), true);
        $countries = array_map(function ($country) {
            $country['flag'] = $this->isoToFlag($country['code']);
            return $country;
        }, $countriesData);
        usort($countries, fn($a, $b) => $a['name'] <=> $b['name']);

        $profileType = $projet->submission->profile_type ?? 'student';

        $categorieNom = match ($profileType) {
            'student' => 'Étudiant',
            'startup' => 'Startup',
            'other'   => 'Porteurs de projet',
            default   => 'Inconnue',
        };

        $categorie = (object) ['nom' => $categorieNom, 'slug' => $profileType];

        $allCategories = collect([
            (object) ['nom' => 'Étudiant', 'slug' => 'student'],
            (object) ['nom' => 'Startup', 'slug' => 'startup'],
            (object) ['nom' => 'Porteurs de projet', 'slug' => 'other'],
        ]);

        $voteStatusDetails = $this->getVoteStatusDetails();

        return view('vote_secteurs', compact('secteurs', 'projet', 'countries', 'categorie', 'allCategories', 'voteStatusDetails'));
    }

    public function projectData($id)
    {
        $projet = Projet::with('secteur')->findOrFail($id);

        $video = DB::table('liste_preselectionnes')
            ->where('projet_id', $projet->id)
            ->where('is_finaliste', 1)
            ->value('video_demonstration');

        $payload = [
            'id'                 => $projet->id,
            'nom_projet'         => $projet->nom_projet,
            'nom_equipe'         => $projet->nom_equipe,
            'resume'             => $projet->resume,
            'description'        => $projet->description,
            'lien_prototype'     => $projet->lien_prototype,
            'secteur'            => $projet->secteur?->nom ?? null,
            'video_demonstration'=> $video,
        ];

        return response()->json($payload);
    }

    private function checkVoteStatus()
    {
        $globalStatus    = Configuration::where('cle', 'vote_status')->first();
        $startTimeConfig = Configuration::where('cle', 'vote_start_time')->first();
        $endTimeConfig   = Configuration::where('cle', 'vote_end_time')->first();

        $isGloballyInactive = !$globalStatus || $globalStatus->valeur === 'inactive';
        $hasStartTime       = $startTimeConfig && !empty($startTimeConfig->valeur);
        $hasEndTime         = $endTimeConfig && !empty($endTimeConfig->valeur);

        $now = now();

        if ($isGloballyInactive) {
            abort(response()->json([
                'success' => false,
                'message' => 'Le système de vote est actuellement fermé par l\'administrateur.',
            ], 403));
        }

        if ($hasStartTime) {
            $startTime = \Carbon\Carbon::parse($startTimeConfig->valeur);
            if ($now->lessThan($startTime)) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'Le vote n\'est pas encore ouvert. Il ouvrira le ' . $startTime->format('d/m/Y à H:i') . '.',
                ], 403));
            }
        }

        if ($hasEndTime) {
            $endTime = \Carbon\Carbon::parse($endTimeConfig->valeur);
            if ($now->greaterThan($endTime)) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'Le vote est terminé depuis le ' . $endTime->format('d/m/Y à H:i') . '.',
                ], 403));
            }
        }
    }

    private function getVoteStatusDetails(): array
    {
        $globalStatus    = Configuration::where('cle', 'vote_status')->first();
        $startTimeConfig = Configuration::where('cle', 'vote_start_time')->first();
        $endTimeConfig   = Configuration::where('cle', 'vote_end_time')->first();

        $isGloballyInactive = !$globalStatus || $globalStatus->valeur === 'inactive';
        $hasStartTime       = $startTimeConfig && !empty($startTimeConfig->valeur);
        $hasEndTime         = $endTimeConfig && !empty($endTimeConfig->valeur);

        $now = now();

        if ($isGloballyInactive) {
            return [
                'isVoteActive'   => false,
                'inactiveMessage'=> 'Le vote est actuellement fermé.',
            ];
        }

        if ($hasStartTime) {
            $startTime = \Carbon\Carbon::parse($startTimeConfig->valeur);
            if ($now->lessThan($startTime)) {
                return [
                    'isVoteActive'   => false,
                    'inactiveMessage'=> 'Le vote ouvrira le ' . $startTime->format('d/m/Y à H:i') . '.',
                ];
            }
        }

        if ($hasEndTime) {
            $endTime = \Carbon\Carbon::parse($endTimeConfig->valeur);
            if ($now->greaterThan($endTime)) {
                return [
                    'isVoteActive'   => false,
                    'inactiveMessage'=> 'Le vote est terminé.',
                ];
            }
        }

        return [
            'isVoteActive'   => true,
            'inactiveMessage'=> '',
        ];
    }

    private function isoToFlag(string $isoCode): string
    {
        $isoCode = strtoupper($isoCode);
        if (strlen($isoCode) !== 2) {
            return '🏳️';
        }

        $offset = 127397;
        $emoji  = '';

        foreach (str_split($isoCode) as $char) {
            $emoji .= mb_chr(ord($char) + $offset, 'UTF-8');
        }

        return $emoji;
    }
}
