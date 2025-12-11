<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\OrangeSmsController;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\View\View;
use App\Models\Commentaire;
use App\Models\Vote;
use App\Models\Configuration;
use App\Models\Categorie;
use App\Models\Secteur;
use App\Models\Projet;
use Illuminate\Support\Facades\DB ;

class VoteController extends Controller
{
    /**
     * Affiche la page de sélection des catégories de vote.
     *
     * @return View
     */
    public function choixCategorie(): View
    {
        // Les catégories sont fixes, pas besoin de les récupérer de la base de données.
        // On les crée statiquement pour les passer à la vue.
        $categories = collect([
            (object) ['nom' => 'Étudiant', 'slug' => 'student'],
            (object) ['nom' => 'Startup', 'slug' => 'startup'],
            (object) ['nom' => 'Citoyens', 'slug' => 'other'], // Renommé "Autre" en "Citoyens" pour la vue
        ]);

        return view('vote', compact('categories')); 
    }

    /**
     * Affiche la page de vote avec les secteurs et projets filtrés.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request, string $profileType): View
    {
        // On déduit le nom de la catégorie à partir du profile_type pour l'affichage
        $categorieNom = match ($profileType) {
            'student' => 'Étudiant',
            'startup' => 'Startup',
            'other' => 'Citoyens', // Maintenu pour la cohérence
            default => 'Inconnue', // Fallback, mais la route protège déjà
        } ;
        $categorie = (object)['nom' => $categorieNom, 'slug' => $profileType];

        $search = $request->input('search', '');
        // Sous-requête : IDs des projets présélectionnés
    $preselectedProjectIds = DB::table('liste_preselectionnes')
        ->select('projet_id');

    // Secteurs qui ont AU MOINS un projet :
    // - validé
    // - bon profile_type
    // - présent dans liste_preselectionnes

        // On ne veut que les secteurs qui ont au moins un projet validé ET de la bonne catégorie.
         $query = Secteur::whereHas('projets', function ($projetQuery) use ($profileType, $preselectedProjectIds) {
        $projetQuery
        //where('validation_admin', 1) //Pour le moment on ne filtre plus sur la validation admin
            ->whereHas('submission', function ($submissionQuery) use ($profileType) {
                $submissionQuery->where('profile_type', $profileType);
            })
            ->whereIn('id', $preselectedProjectIds);
        });

        // Si un terme de recherche est présent, on applique les filtres
    if ($search) {
        $query->where(function ($q) use ($search, $preselectedProjectIds) {
            $q->where('nom', 'like', '%' . $search . '%')
              ->orWhereHas('projets', function ($subQuery) use ($search, $preselectedProjectIds) {
                  $subQuery
                  //where('validation_admin', 1) //Pour le moment on ne filtre plus sur la validation admin
                      ->whereIn('id', $preselectedProjectIds)
                      ->where(function ($subSubQuery) use ($search) {
                          $subSubQuery->where('nom_projet', 'like', '%' . $search . '%')
                                      ->orWhere('nom_equipe', 'like', '%' . $search . '%');
                      });
              });
        });
      
    }

  // Eager loading uniquement des projets présélectionnés
    $query->with(['projets' => function ($projetQuery) use ($search, $profileType, $preselectedProjectIds) {
        // Join with liste_preselectionnes to include video_demonstration and avoid N+1 lookups
        $projetQuery
            ->leftJoin('liste_preselectionnes', 'projets.id', '=', 'liste_preselectionnes.projet_id')
            ->select('projets.*', 'liste_preselectionnes.video_demonstration')
            //where('validation_admin', 1) //Pour le moment on ne filtre plus sur la validation admin
            ->whereHas('submission', fn($q) => $q->where('profile_type', $profileType))
            ->whereIn('projets.id', $preselectedProjectIds)
            ->when($search, function ($q) use ($search) {
                $q->where('nom_projet', 'like', "%{$search}%")
                  ->orWhere('nom_equipe', 'like', "%{$search}%");
            })
            ->orderBy('nom_projet');
    }]);

    $secteurs = $query->orderBy('nom')->get();
        // On charge les données des pays depuis le fichier JSON
        $countriesData = json_decode(File::get(public_path('data/countries.json')), true);
        $countries = array_map(function ($country) {
            $country['flag'] = $this->isoToFlag($country['code']);
            return $country;
        }, $countriesData);
        // On trie les pays par nom pour un affichage plus convivial
        usort($countries, fn($a, $b) => $a['name'] <=> $b['name']);

        // On charge la liste de toutes les catégories pour le menu de navigation
        $allCategories = collect([
            (object) ['nom' => 'Étudiant', 'slug' => 'student'],
            (object) ['nom' => 'Startup', 'slug' => 'startup'],
            (object) ['nom' => 'Citoyens', 'slug' => 'other'],
        ]);

        // 🚀 On récupère le statut du vote pour le passer à la vue
        $voteStatusDetails = $this->getVoteStatusDetails();

        // --- Server-side pagination for projects (same UX as classement) ---
        $perPage = (int) $request->query('per_page', 5);

        $projetsQuery = Projet::with(['secteur', 'listePreselectionne'])
            ->whereHas('submission', fn($q) => $q->where('profile_type', $profileType))
            ->whereIn('id', $preselectedProjectIds)
            ->when($search, function ($q) use ($search) {
                $q->where('nom_projet', 'like', "%{$search}%")
                  ->orWhere('nom_equipe', 'like', "%{$search}%");
            })
            ->orderBy('nom_projet');

        $projets = $projetsQuery->paginate($perPage)->withQueryString();


        return view('vote_secteurs', compact('secteurs', 'projets', 'countries', 'voteStatusDetails', 'categorie', 'allCategories'));
    }

    /**
     * Gère les requêtes de recherche AJAX et retourne les résultats en JSON.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechercheAjax(Request $request): \Illuminate\Http\JsonResponse
    {
      $search = $request->input('search', '');

    // Sous-requête : IDs des projets présélectionnés
    $preselectedProjectIds = DB::table('liste_preselectionnes')
        ->select('projet_id');

    $query = Secteur::query();

    if ($search) {
        $query->where(function ($q) use ($search, $preselectedProjectIds) {
            $q->where('nom', 'like', '%' . $search . '%')
              ->orWhereHas('projets', function ($subQuery) use ($search, $preselectedProjectIds) {
                  $subQuery
                 // where('validation_admin', 1) //Pour le moment on ne filtre plus sur la validation admin
                      ->whereIn('id', $preselectedProjectIds)
                      ->where(function ($subSubQuery) use ($search) {
                          $subSubQuery->where('nom_projet', 'like', '%' . $search . '%')
                                      ->orWhere('nom_equipe', 'like', '%' . $search . '%');
                      });
              });
        });
    } else {
        // Même sans recherche, on veut seulement les secteurs qui ont des projets présélectionnés
        $query->whereHas('projets', function ($subQuery) use ($preselectedProjectIds) {
            $subQuery
            //where('validation_admin', 1) //Pour le moment on ne filtre plus sur la validation admin
                ->whereIn('id', $preselectedProjectIds);
        });
    }

    $query->with(['projets' => function ($projetQuery) use ($search, $preselectedProjectIds) {
        // Join with liste_preselectionnes to include video_demonstration
        $projetQuery
            ->leftJoin('liste_preselectionnes', 'projets.id', '=', 'liste_preselectionnes.projet_id')
            ->select('projets.*', 'liste_preselectionnes.video_demonstration')
            //where('validation_admin', 1) //Pour le moment on ne filtre plus sur la validation admin
            ->whereIn('projets.id', $preselectedProjectIds);

        if ($search) {
            $projetQuery->where(function ($subQuery) use ($search) {
                $subQuery->where('nom_projet', 'like', '%' . $search . '%')
                         ->orWhere('nom_equipe', 'like', '%' . $search . '%');
            });
        }

        $projetQuery->orderBy('nom_projet');
    }])->orderBy('nom');

    $secteurs = $query->get()->filter(fn ($secteur) => $secteur->projets->isNotEmpty())->values();

    return response()->json($secteurs);

    }

    /**
     * Valide les informations du votant (depuis la modale), génère un OTP et l'envoie (simulation).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function envoyerOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        // Valider les champs reçus (on attend country_code + telephone_display pour pouvoir normaliser)
        $validated = $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'country_code' => 'required|string',
            'telephone_display' => 'required|string',
            'nom_votant' => 'nullable|string|max:255',
            // On rend le token reCAPTCHA requis uniquement si la fonctionnalité est activée
            'recaptcha_token' => config('services.recaptcha.enabled', false) ? 'required|string' : 'nullable|string',
        ]);

        $this->checkVoteStatus();

        $projetId = $validated['projet_id'];
        $nomVotant = $validated['nom_votant'] ?? null;

        // Normalisation du numéro en E.164
        $countryCode = $validated['country_code'];
        $telephoneDisplay = $validated['telephone_display'];

        $e164 = null;
        // Essayer d'utiliser libphonenumber si présent
        if (class_exists(PhoneNumberUtil::class)) {
            try {
                $phoneUtil = PhoneNumberUtil::getInstance();
                // Construire une chaîne brute: si countryCode contient le +, on conserve
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
                // En cas d'erreur inattendue, fallback ci-dessous
                $e164 = null;
            }
        }

        // Fallback simple si libphonenumber non installé ou parsing échoue
        if (!$e164) {
            $digitsCountry = preg_replace('/\D+/', '', $countryCode);
            $digitsLocal = preg_replace('/\D+/', '', $telephoneDisplay);
            if (empty($digitsCountry) || empty($digitsLocal)) {
                return response()->json(['success' => false, 'message' => 'Numéro de téléphone invalide.'], 422);
            }
            $e164 = '+' . $digitsCountry . $digitsLocal;
        }

        $telephone = $e164;

        // --- Vérifications côté serveur : nombre total de votes et vote par secteur ---
        try {
            $totalVerified = DB::table('vote_publics')
                ->where('telephone', $telephone)
                ->where('est_verifie', true)
                ->count();

            if ($totalVerified >= 3) {
                return response()->json(['success' => false, 'message' => 'Vous avez déjà utilisé vos 3 votes autorisés.'], 409);
            }

            // Vérifier si l'utilisateur a déjà voté pour le secteur de ce projet
            $projet = Projet::find($projetId);
            $secteurId = $projet?->secteur_id;

            if ($secteurId) {
                $alreadyInSector = DB::table('vote_publics')
                    ->join('projets', 'vote_publics.projet_id', '=', 'projets.id')
                    ->where('vote_publics.telephone', $telephone)
                    ->where('projets.secteur_id', $secteurId)
                    ->where('vote_publics.est_verifie', true)
                    ->exists();

                if ($alreadyInSector) {
                    return response()->json(['success' => false, 'message' => 'Vous avez déjà voté dans cette catégorie.'], 409);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Erreur lors des vérifications pré-OTP', ['error' => $e->getMessage()]);
            // En cas d'erreur lors des vérifs, on continue mais on logue
        }

        // --- Vérification conditionnelle du token reCAPTCHA ---
        if (config('services.recaptcha.enabled', false)) {
            $recaptchaToken = $validated['recaptcha_token'];

// NE SURTOUT PAS OUBLIER D'ACTIVER LA CERTIFICATION ET LES SECURITES QUAND JE PASSERAIS EN PRODUCTION
            $response = Http::withoutVerifying() // <-- Ajout pour désactiver la vérification SSL
                ->asForm()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $recaptchaToken,
                    'remoteip' => $request->ip(), // Optionnel mais recommandé pour une meilleure détection
                ]);

            $body = $response->json();

            // Un score de 0.7 est un bon point de départ, vous pouvez l'ajuster
            if (!isset($body['success']) || !$body['success'] || (isset($body['score']) && $body['score'] < 0.7)) {
                // reCAPTCHA a échoué, considérer comme un bot
                return response()->json(['success' => false, 'message' => 'La vérification de sécurité a échoué. Veuillez réessayer.'], 422);
            }
        }

        // --- Logique d'envoi d'OTP ---

        // 1. Générer un code OTP (ex: un nombre aléatoire à 6 chiffres)
        $otp = random_int(100000, 999999);

        // 2. Stocker les informations en session avec une date d'expiration (ex: 10 minutes)
        $request->session()->put('otp_data', [
            'projet_id' => $projetId,
            'telephone' => $telephone,
            'nom_votant' => $nomVotant,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0, // Ajout du compteur de tentatives
        ]);

        // 3. Envoyer le code via Orange SMS
        try {
            $orangeSms = new OrangeSmsController();
            $smsResponse = $orangeSms->sendOtp(new Request([
                'phone' => $telephone,
                'otp' => $otp, // On passe l'OTP généré pour qu'il soit utilisé dans le message
            ]));

            $smsData = $smsResponse->getData();

            if (!isset($smsData->success) || !$smsData->success) {
                // Si l'envoi échoue, on logue l'erreur et on renvoie un message générique
                Log::error('Échec de l\'envoi de l\'OTP via Orange SMS', ['response' => $smsData]);
                return response()->json(['success' => false, 'message' => 'Erreur lors de l\'envoi du code. Veuillez réessayer.'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erreur Orange SMS: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'envoi du code. Veuillez vérifier le numéro de téléphone et réessayer.'], 500);
        }

        // 4. Renvoyer une réponse JSON de succès au client.
        $responseData = ['success' => true, 'message' => 'Un code OTP a été envoyé.'];

        // 🚀 IMPORTANT: Pour le développement uniquement. Ne jamais logger l'OTP en clair.
        // Journaliser uniquement un identifiant non sensible (ex: 4 derniers chiffres) pour faciliter le debug.
        try {
            $digitsOnly = preg_replace('/\D+/', '', $telephone);
            $last4 = substr($digitsOnly, -4);
        } catch (\Throwable $e) {
            $last4 = null;
        }
        Log::info('OTP généré et envoyé (valeur non enregistrée)', ['phone_last4' => $last4]);

        return response()->json($responseData);
    }

    /**
     * Vérifie le code OTP soumis par l'utilisateur et enregistre le vote.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifierOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'otp' => 'required|string|digits:6',
        ]);

        $this->checkVoteStatus();

        $submittedOtp = $validated['otp'];
        $otpData = $request->session()->get('otp_data');

        // 1. Vérifier si les données OTP sont présentes en session
        if (!$otpData) {
            return response()->json(['success' => false, 'message' => 'Session OTP expirée ou invalide. Veuillez recommencer le processus de vote.'], 400);
        }

        // 2. Vérifier si l'OTP a expiré
        if (now()->greaterThan($otpData['expires_at'])) {
            $request->session()->forget('otp_data'); // Nettoyer la session expirée
            return response()->json(['success' => false, 'message' => 'Le code OTP a expiré. Veuillez demander un nouveau code.'], 400);
        }

        // 3. Vérifier le nombre de tentatives
        $maxAttempts = 5; // Définir le nombre maximum de tentatives
        if (isset($otpData['attempts']) && $otpData['attempts'] >= $maxAttempts) {
            $request->session()->forget('otp_data'); // Nettoyer la session
            return response()->json(['success' => false, 'message' => 'Trop de tentatives incorrectes. Veuillez recommencer le processus de vote.'], 429); // 429 Too Many Requests
        }

        // 3. Comparer l'OTP soumis avec l'OTP stocké
        if ($submittedOtp !== (string)$otpData['otp']) {
            // Incrémenter le compteur de tentatives
            $otpData['attempts'] = ($otpData['attempts'] ?? 0) + 1;
            $request->session()->put('otp_data', $otpData);

            return response()->json(['success' => false, 'message' => 'Code OTP incorrect. Veuillez réessayer.'], 401);
        }

        // 4. Si l'OTP est valide, effectuer des vérifications finales côté serveur (atomiques)
        $phone = $otpData['telephone'];
        $projetId = $otpData['projet_id'];

        try {
            // Re-vérifier le compteur global des votes vérifiés pour ce téléphone
            $totalVerified = DB::table('vote_publics')
                ->where('telephone', $phone)
                ->where('est_verifie', true)
                ->count();

            if ($totalVerified >= 3) {
                $request->session()->forget('otp_data');
                return response()->json(['success' => false, 'message' => 'Vous avez déjà utilisé vos 3 votes autorisés.'], 409);
            }

            // Vérifier qu'il n'a pas déjà voté pour le même secteur
            $projet = Projet::find($projetId);
            $secteurId = $projet?->secteur_id;

            if ($secteurId) {
                $alreadyInSector = DB::table('vote_publics')
                    ->join('projets', 'vote_publics.projet_id', '=', 'projets.id')
                    ->where('vote_publics.telephone', $phone)
                    ->where('projets.secteur_id', $secteurId)
                    ->where('vote_publics.est_verifie', true)
                    ->exists();

                if ($alreadyInSector) {
                    $request->session()->forget('otp_data');
                    return response()->json(['success' => false, 'message' => 'Vous avez déjà voté dans cette catégorie.'], 409);
                }
            }

            // Enregistrer le vote dans une transaction pour plus de sûreté
            DB::transaction(function () use ($projetId, $phone) {
                Vote::create([
                    'projet_id' => $projetId,
                    'telephone' => $phone,
                    'token' => Str::uuid(),
                    'est_verifie' => true,
                ]);
            });

            // Nettoyer les données OTP de la session après un vote réussi
            $request->session()->forget('otp_data');

            return response()->json(['success' => true, 'message' => 'Votre vote a été enregistré avec succès ! Merci de votre participation.']);

        } catch (QueryException $e) {
            // Gestion spécifique des erreurs de contrainte (duplicate key)
            $sqlState = $e->getCode();
            Log::warning('QueryException lors de la création du vote', ['code' => $sqlState, 'message' => $e->getMessage()]);

            // Nettoyer la session OTP pour forcer le flux à redemarrer
            $request->session()->forget('otp_data');

            return response()->json(['success' => false, 'message' => 'Ce numéro a déjà été utilisé pour voter pour ce projet.'], 409);

        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'enregistrement du vote: ' . $e->getMessage());
            $request->session()->forget('otp_data');
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue lors de l\'enregistrement de votre vote. Veuillez réessayer.'], 500);
        }
    }

//Fonction pour afficher un projet en particulier
public function afficherProjet($id)
{
    $projet = \App\Models\Projet::with('secteur')->findOrFail($id);
    // Charger les secteurs (avec leurs projets) -- pour compatibilité avec la vue
    $secteurs = \App\Models\Secteur::with('projets')->get();

    // On charge également les pays ici car la même vue est utilisée
    $countriesData = json_decode(File::get(public_path('data/countries.json')), true);
    $countries = array_map(function ($country) {
        $country['flag'] = $this->isoToFlag($country['code']);
        return $country;
    }, $countriesData);
    usort($countries, fn($a, $b) => $a['name'] <=> $b['name']);


    // Déterminer la catégorie du projet (fallback à 'student' si indisponible)
    $profileType = $projet->submission->profile_type ?? 'student';
    $categorieNom = match ($profileType) {
        'student' => 'Étudiant',
        'startup' => 'Startup',
        'other' => 'Citoyens',
        default => 'Inconnue',
    };
    $categorie = (object)['nom' => $categorieNom, 'slug' => $profileType];

    // Liste statique des catégories pour le menu
    $allCategories = collect([
        (object) ['nom' => 'Étudiant', 'slug' => 'student'],
        (object) ['nom' => 'Startup', 'slug' => 'startup'],
        (object) ['nom' => 'Citoyens', 'slug' => 'other'],
    ]);

    // Statut du vote pour la vue
    $voteStatusDetails = $this->getVoteStatusDetails();

    return view('vote_secteurs', compact('secteurs', 'projet', 'countries', 'categorie', 'allCategories', 'voteStatusDetails'));
}

    /**
     * Retourne un petit payload JSON pour un projet donné (données non sensibles).
     * Utilisé pour charger à la demande les modales côté client et éviter d'embarquer
     * de gros objets ou des champs sensibles dans la page HTML.
     */
    public function projectData($id)
    {
        $projet = \App\Models\Projet::with('secteur')->findOrFail($id);

        // try to fetch video_demonstration from liste_preselectionnes if present
        $video = DB::table('liste_preselectionnes')->where('projet_id', $projet->id)->value('video_demonstration');

        $payload = [
            'id' => $projet->id,
            'nom_projet' => $projet->nom_projet,
            'nom_equipe' => $projet->nom_equipe,
            'resume' => $projet->resume,
            'description' => $projet->description,
            'lien_prototype' => $projet->lien_prototype,
            'secteur' => $projet->secteur?->nom ?? null,
            'video_demonstration' => $video,
        ];

        return response()->json($payload);
    }

    /**
     * Vérifie si le système de vote est actuellement actif.
     * Interrompt la requête avec une réponse JSON si le vote est fermé.
     */
    private function checkVoteStatus()
    {
        $globalStatus = Configuration::where('cle', 'vote_status')->first();
        $startTimeConfig = Configuration::where('cle', 'vote_start_time')->first();
        $endTimeConfig = Configuration::where('cle', 'vote_end_time')->first();

        $isGloballyInactive = !$globalStatus || $globalStatus->valeur === 'inactive';
        $hasStartTime = $startTimeConfig && !empty($startTimeConfig->valeur);
        $hasEndTime = $endTimeConfig && !empty($endTimeConfig->valeur);

        $now = now();

        if ($isGloballyInactive) {
            abort(response()->json(['success' => false, 'message' => 'Le système de vote est actuellement fermé par l\'administrateur.'], 403));
        }

        if ($hasStartTime) {
            $startTime = \Carbon\Carbon::parse($startTimeConfig->valeur);
            if ($now->lessThan($startTime)) {
                abort(response()->json(['success' => false, 'message' => 'Le vote n\'est pas encore ouvert. Il ouvrira le ' . $startTime->format('d/m/Y à H:i') . '.'], 403));
            }
        }

        if ($hasEndTime) {
            $endTime = \Carbon\Carbon::parse($endTimeConfig->valeur);
            if ($now->greaterThan($endTime)) {
                abort(response()->json(['success' => false, 'message' => 'Le vote est terminé depuis le ' . $endTime->format('d/m/Y à H:i') . '.'], 403));
            }
        }
    }

    /**
     * Récupère et retourne le statut détaillé du vote sans interrompre la requête.
     *
     * @return array{isVoteActive: bool, inactiveMessage: string}
     */
    private function getVoteStatusDetails(): array
    {
        $globalStatus = Configuration::where('cle', 'vote_status')->first();
        $startTimeConfig = Configuration::where('cle', 'vote_start_time')->first();
        $endTimeConfig = Configuration::where('cle', 'vote_end_time')->first();

        $isGloballyInactive = !$globalStatus || $globalStatus->valeur === 'inactive';
        $hasStartTime = $startTimeConfig && !empty($startTimeConfig->valeur);
        $hasEndTime = $endTimeConfig && !empty($endTimeConfig->valeur);

        $now = now();

        if ($isGloballyInactive) {
            return ['isVoteActive' => false, 'inactiveMessage' => 'Le vote est actuellement fermé.'];
        }

        if ($hasStartTime) {
            $startTime = \Carbon\Carbon::parse($startTimeConfig->valeur);
            if ($now->lessThan($startTime)) {
                return ['isVoteActive' => false, 'inactiveMessage' => 'Le vote ouvrira le ' . $startTime->format('d/m/Y à H:i') . '.'];
            }
        }

        if ($hasEndTime) {
            $endTime = \Carbon\Carbon::parse($endTimeConfig->valeur);
            if ($now->greaterThan($endTime)) {
                return ['isVoteActive' => false, 'inactiveMessage' => 'Le vote est terminé.'];
            }
        }

        return ['isVoteActive' => true, 'inactiveMessage' => ''];
    }
    /**
     * Convertit un code pays ISO 3166-1 alpha-2 en son émoji drapeau correspondant.
     *
     * @param string $isoCode Le code ISO à 2 lettres (ex: "FR", "US").
     * @return string L'émoji drapeau.
     */
    private function isoToFlag(string $isoCode): string
{
    $isoCode = strtoupper($isoCode);
    if (strlen($isoCode) !== 2) {
        return '🏳️'; // drapeau blanc par défaut
    }

    $offset = 127397;
    $emoji = '';
    foreach (str_split($isoCode) as $char) {
        $emoji .= mb_chr(ord($char) + $offset, 'UTF-8');
    }
    return $emoji;
}

}
