<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votez pour votre projet - GovAthon</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- 2. On place le script qui utilise Alpine APRÈS, et on le "defer" aussi pour qu'il attende --}}

    <!-- Police -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

</head>
<body class="bg-black text-white flex flex-col min-h-screen bg-cover bg-center bg-fixed bg-image-custom font-poppins">

    <x-header />

    <main class="flex-grow container mx-auto px-4 py-12 flex items-center">
        <div class="bg-black bg-opacity-60 p-8 rounded-lg shadow-2xl max-w-6xl mx-auto">
            <div class="text-center mb-4">
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <div>
                        <button @click="open = !open" type="button" class="inline-flex justify-center items-center w-full rounded-md px-4 py-2 text-2xl sm:text-3xl font-bold text-yellow-400 hover:text-yellow-300 focus:outline-none" id="menu-button" aria-expanded="true" aria-haspopup="true">
                            Projets : <span class="text-white ml-2">{{ $categorie->nom }}</span>
                            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                         role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="py-1" role="none">
                            @foreach($allCategories->where('slug', '!=', $categorie->slug) as $cat)
                                <a href="{{ route('vote.secteurs', ['profile_type' => $cat->slug]) }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 text-sm" role="menuitem" tabindex="-1">{{ $cat->nom }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-center text-gray-300 mb-8">Recherchez un projet, une équipe ou un secteur, puis votez pour votre préféré.</p>

            <!-- Barre de recherche -->
            <div class="mb-8">
            <div class="mb-8"> 
                <form action="{{ route('vote.secteurs', ['profile_type' => $categorie->slug]) }}" method="GET">
                    <div class="relative">
                        <input type="text" id="search-input" name="search" placeholder="Rechercher un projet, une équipe ou un secteur..."
                               class="w-full bg-gray-900/50 border border-gray-700 rounded-lg py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                               value="{{ request('search') }}" autocomplete="off">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg id="search-icon" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </form>
            </div>
            </div>

            <!-- Conteneur Alpine.js global -->
            {{-- 🚀 Étape 1.1: Ajout des états pour la modale de vote (voteStep, messages, etc.) --}}
                <div 
    x-data="{
        showModal: false,
        modalProjet: null,
        showVoteModal: false,
        voteProjet: null,

        // 🚀 Variables pour le statut du vote
        isVoteActive: {{ json_encode($voteStatusDetails['isVoteActive']) }},
        inactiveMessage: {{ json_encode($voteStatusDetails['inactiveMessage']) }},

        voteStep: 1,
        isLoading: false,
        errorMessage: '',
        successMessage: '',
        descriptionExpanded: false,

        // Notification temporaire lorsque le vote est inactif
        inactiveNoticeVisible: false,
        showInactiveNotice() {
            this.inactiveNoticeVisible = true;
            setTimeout(() => { this.inactiveNoticeVisible = false }, 1000);
        }
    }"

    x-init="
        // Si le vote est inactif, on initialise la modale
        if (!isVoteActive) {
            voteStep = 3;
            errorMessage = inactiveMessage;
        }
    "

    @keydown.escape.window="showModal = false; showVoteModal = false"
>



                {{-- Message "aucun résultat" géré par JS --}}
                <div id="no-results-message" class="text-center py-12" style="display: none;">
                    <p class="text-xl text-gray-400">Aucun secteur ou projet trouvé.</p>
                    <a href="{{ route('vote.secteurs', ['profile_type' => $categorie->slug]) }}" class="mt-4 inline-block text-yellow-400 hover:text-yellow-300">
                        &larr; Revenir à la liste complète
                    </a>
                </div>

                <!-- Notice temporaire en haut à droite pour informer que le vote est désactivé -->
                <div x-show="inactiveNoticeVisible"
                     x-transition
                     class="fixed top-24 right-6 z-50 bg-red-800/90 text-red-100 px-4 py-3 rounded-lg shadow-lg flex items-center gap-3"
                     style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-200" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3a1 1 0 102 0V7zm-1 7a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5z" clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm">
                        <div class="font-semibold">Vote désactivé</div>
                        <div class="text-xs text-red-200">{{ $voteStatusDetails['inactiveMessage'] }}</div>
                    </div>
                </div>
                <!-- Tableau des projets -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-900/50 hidden md:table-header-group">
                            <tr>
                                <th class="p-4 text-lg font-semibold text-yellow-400">Secteur</th>
                                <th class="p-4 text-lg font-semibold text-yellow-400">Nom de l'équipe</th>
                                <th class="p-4 text-lg font-semibold text-yellow-400">Nom du projet</th>
                                <th class="p-4 text-lg font-semibold text-yellow-400 text-center">Vote</th>
                            </tr>
                        </thead>
                        <tbody id="projects-table-body">
                            @foreach ($secteurs as $secteur)
                                @forelse ($secteur->projets as $projet)
                                    <tr class="block md:table-row border-b border-gray-700 hover:bg-gray-900/30 transition-colors">
                                        <td class="block md:table-cell p-4" data-label="Secteur : ">{{ $secteur->nom }}</td>
                                        <td class="block md:table-cell p-4" data-label="Équipe : ">{{ $projet->nom_equipe }}</td>
                                        <td class="block md:table-cell p-4 font-semibold" data-label="Projet : ">{{ $projet->nom_projet }}</td>
                                        <td class="block md:table-cell p-4 text-center align-middle">
                                                   <div class="relative flex flex-col md:flex-row items-center justify-center gap-2">

                                                <!-- Bouton Détails  -->
                                                <button 
                                                    type="button"
                                                    class="group flex items-center justify-center gap-2 w-full md:w-auto px-4 py-2 text-sm font-semibold text-gray-300 bg-transparent border border-gray-600 rounded-lg hover:border-yellow-400 hover:text-white transition-all duration-300 transform hover:scale-105"
                                                    @click="modalProjet = @js($projet); showModal = true; descriptionExpanded = false">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Détails
                                                </button>

                                                <!-- Bouton Voter  -->
                                                <button
                                                    data-role="vote-btn"
                                                    type="button"
                                                    class="group flex items-center justify-center gap-2 w-full md:w-auto px-4 py-2 text-sm font-bold rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-yellow-400/20"
                                                    :class="{
                                                        'bg-yellow-400 text-gray-900 hover:bg-yellow-300': isVoteActive,
                                                        'bg-gray-600 text-gray-300 cursor-not-allowed': !isVoteActive
                                                    }"
                                                    {{-- Réinitialise l'état de la modale à chaque ouverture --}}
                                                    :disabled="!isVoteActive"
                                                    @click="
                                                        voteProjet = @js($projet); // Toujours définir le projet pour le contexte
                                                        showVoteModal = true; // Toujours ouvrir la modale
                                                        voteStep = isVoteActive ? 1 : 3; // Étape 1 si actif, Étape 3 (message) si inactif
                                                        errorMessage = isVoteActive ? '' : inactiveMessage; // Message d'erreur si inactif
                                                        successMessage = ''; // Réinitialiser le message de succès
                                                    ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Voter
                                                </button>
                                                <!-- Overlay pour capter le clic quand le vote est INactif -->
                                                <button x-show="!isVoteActive" @click.prevent="showInactiveNotice()" class="absolute inset-0 w-full h-full z-20 bg-transparent" aria-hidden="true"></button>

                                                <!-- Bouton Partager -->
                                                <button 
                                                    type="button"
                                                    class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                    title="Partager ce projet"
                                                    onclick="shareProject('{{ route('vote.afficherProjet', ['id' => $projet->id]) }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            @endforeach
                        </tbody>
                    </table>
                </div>
                

                <!-- Fenêtre modale DÉTAILS -->
                <div 
                        x-show="showModal"
                        style="display: none;"
                        class="fixed inset-0 bg-black/5 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">

                        <div 
                            @click.away="showModal = false"
                            class="bg-gray-900/95  border-yellow-400/30 rounded-lg shadow-2xl max-w-2xl w-full text-white relative flex flex-col"
                            style="max-height: 90vh;"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90">

                            <!-- En-tête -->
                            <div class="p-6 border-b border-gray-700 flex-shrink-0">
                                <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl leading-none">&times;</button>
                                <h2 class="text-2xl sm:text-3xl font-bold text-gray-400 mb-2" x-text="modalProjet?.nom_projet"></h2>
                                <p class="text-md sm:text-lg text-gray-300">
                                    par <span class="font-semibold" x-text="modalProjet?.nom_equipe"></span>
                                </p>
                            </div>

                            <!-- Contenu -->
                            <div class="p-6 space-y-4 text-gray-200 overflow-y-auto scrollbar-thin">
                                <p><strong class="text-gray-300">Résumé :</strong> 
                                    <span class="whitespace-pre-wrap" x-text="modalProjet?.resume"></span>
                                </p>
                                <div>
                                    <strong class="text-gray-300">Description :</strong>
                                    <div class="whitespace-pre-wrap" :class="{'max-h-24 overflow-hidden': !descriptionExpanded && modalProjet?.description.length > 250}">
                                        <span x-text="modalProjet?.description"></span>
                                    </div>
                                    <button 
                                        x-show="modalProjet?.description.length > 250"
                                        @click="descriptionExpanded = !descriptionExpanded"
                                        class="text-yellow-400 hover:text-yellow-300 mt-2 text-sm">
                                        <span x-text="descriptionExpanded ? 'Voir moins' : 'Voir plus'"></span>
                                    </button>
                                </div>
                                <p x-show="modalProjet?.lien_prototype">
                                    <strong class="text-yellow-300">Prototype :</strong> 
                                    <a :href="modalProjet?.lien_prototype" target="_blank" class="text-blue-400 hover:underline break-all" x-text="modalProjet?.lien_prototype"></a>
                                </p>
                            </div>

                        </div>
                </div>

                <!-- Fenêtre modale VOTE -->
                <div
                    x-show="showVoteModal"
                    style="display: none;"
                    class="fixed inset-0 bg-black/5  backdrop-blur-sm flex items-center justify-center z-50 p-4"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">

                    <div
                        @click.away="if (!isLoading) showVoteModal = false"
                        class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg shadow-2xl max-w-lg w-full text-white relative flex flex-col"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90">
                        <div class="p-6 border-b border-gray-700 flex-shrink-0 relative">
                            <button @click="if (!isLoading) showVoteModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors p-1 rounded-full hover:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <h2 class="text-2xl font-bold text-yellow-400 mb-2">
                                Voter pour : <span x-text="voteProjet?.nom_projet"></span>
                            </h2>
                            <p class="text-gray-300">
                                Équipe : <span class="font-semibold" x-text="voteProjet?.nom_equipe"></span>
                            </p>
                            <!-- Indicateur de progression -->
                            <div x-show="voteStep === 1 || voteStep === 2" class="absolute bottom-0 left-6 translate-y-1/2 bg-gray-800 px-3 py-1 rounded-full text-xs text-yellow-300 border border-gray-700">
                                <span x-text="`Étape ${voteStep} sur 2`"></span>
                            </div>
                        </div>

                        <!-- Contenu (Formulaire) -->
                        <div class="p-6 space-y-4">
                            @csrf

                            {{-- 🚀 Étape 4: Affichage des erreurs de validation --}}
                            @if ($errors->any())
                                <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg relative" role="alert">
                                    <strong class="font-bold">Oups ! Une erreur est survenue.</strong>
                                    <ul class="mt-2 list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {{-- Fin du bloc d'erreurs --}}

                            {{-- 🚀 Étape 1: Formulaire Nom & Téléphone --}}
                            <div x-show="voteStep === 1">
                                <form id="otp-request-form"
                                      class="space-y-4"
                                      data-send-otp-url="{{ route('vote.envoyerOtp') }}"
                                      data-recaptcha-key="{{ config('services.recaptcha.site_key') }}"
                                      onsubmit="return false;">
                                    <input type="hidden" name="projet_id" :value="voteProjet?.id">
                                    <input type="hidden" name="telephone" id="telephone_full">
                                    <input type="hidden" name="recaptcha_token" id="recaptcha-token">
                                    <div>
                                        <label for="nom_votant" class="block mb-2 text-sm font-medium text-gray-300">Votre nom (Optionnel)</label>
                                        <input type="tel" id="nom_votant" name="nom_votant"
                                               class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                               placeholder="Ex: Paul David Mbaye">
                                    </div>

                                    <div>
                                        <label for="telephone_display" class="block mb-2 text-sm font-medium text-gray-300">Votre numéro de téléphone</label>
                                        <div class="flex">
                                            <select id="country_code" name="country_code" class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-200 bg-gray-800 border border-gray-600 rounded-l-lg hover:bg-gray-700 focus:ring-2 focus:outline-none focus:ring-yellow-400">
                                                @foreach($countries as $country)
                                                    <option value="{{ $country['dial_code'] }}" @if($country['code'] === 'SN') selected @endif>
                                                        {!! $country['flag'] !!} {{ $country['dial_code'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="relative w-full">
                                                <input type="tel" id="telephone_display" name="telephone_display"
                                                       class="block p-2.5 w-full z-20 text-sm text-white bg-gray-700/50 rounded-r-lg border-l-0 border border-gray-600 focus:ring-2 focus:outline-none focus:ring-yellow-400"
                                                       placeholder="" required>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-400">Vous recevrez un code unique par SMS pour valider votre vote.</p>
                                    </div>

                                    <div class="pt-4 flex justify-center">
                                        <div class="rainbow relative z-0 overflow-hidden p-0.5 flex items-center justify-center rounded-full hover:scale-105 transition duration-300 active:scale-100 w-full">
                                            <button type="button" id="submit-vote-btn"
                                                    class="w-full px-8 text-sm py-3 text-white rounded-full font-medium bg-gray-900 flex items-center justify-center"
                                                    :disabled="isLoading">
                                                <span x-show="!isLoading">Recevoir le code de vote</span>
                                                <span x-show="isLoading">Envoi en cours...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- 🚀 Étape 2: Formulaire de saisie de l'OTP --}}
                            <div x-show="voteStep === 2" style="display: none;">
                                <form id="otp-verify-form"
                                      class="space-y-4"
                                      data-verify-otp-url="{{ route('vote.verifierOtp') }}"
                                      onsubmit="return false;">
                                    <p class="text-center text-gray-300">Un code a été envoyé. Veuillez le saisir ci-dessous.</p>
                                    <div>
                                        <label for="otp" class="block mb-2 text-sm font-medium text-gray-300">Code de vérification (OTP)</label>
                                        <input type="text" id="otp" name="otp"
                                               class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-2 px-3 text-white text-center text-2xl tracking-[1em]"
                                               placeholder="------" required maxlength="6" pattern="\d{6}">
                                    </div>
                                    <div class="pt-4">
                                        <button type="button" id="submit-otp-btn"
                                                class="w-full skew-btn bg-emerald-600 text-white hover:text-white flex items-center justify-center"
                                                :disabled="isLoading">
                                            <span x-show="!isLoading">Valider le vote</span>
                                            <span x-show="isLoading">Validation...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- 🚀 Étape 3: Messages de succès ou d'erreur --}}
                            <div x-show="voteStep === 3" style="display: none;" class="text-center py-8">
                                <div x-show="successMessage" class="text-emerald-400">
                                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-xl font-semibold" x-text="successMessage"></p>
                                </div>
                                <div x-show="errorMessage" class="text-red-400">
                                     <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-xl font-semibold" x-text="errorMessage"></p>
                                </div>
                                <button @click="showVoteModal = false" class="mt-6 skew-btn bg-gray-600 text-white">Fermer</button>
                            </div>

                            {{-- Affichage des messages d'erreur globaux --}}
                            <div x-show="errorMessage && (voteStep === 1 || voteStep === 2)"
                                 class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg relative mt-4" role="alert">
                                <p x-text="errorMessage"></p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-footer />

    <!-- 🚀 Étape 1: Chargement de la bibliothèque reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

</body> 
</html>