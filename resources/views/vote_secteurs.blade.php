<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votez pour votre projet - GovAthon</title>

    <!-- Alpine.js et Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- 1. Alpine se  charge en premier --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- 2. On place le script qui utilise Alpine APRÈS, et on le "defer" aussi pour qu'il attende --}}
   
   <script>
        console.log('Le bloc <script> de la page vote_secteurs est en cours de lecture.');

        document.addEventListener('alpine:init', () => {
            const alpineScope = Alpine.$data(document.querySelector('[x-data]'));
            console.log('✅ Alpine prêt', alpineScope);

            const otpRequestForm = document.getElementById('otp-request-form');
            const submitVoteBtn = document.getElementById('submit-vote-btn');
            console.log('Bouton "Recevoir le code" trouvé:', submitVoteBtn); // <-- PREMIER CONSOLE.LOG
            const otpVerifyForm = document.getElementById('otp-verify-form');
            const submitOtpBtn = document.getElementById('submit-otp-btn');

            // Envoi OTP
            submitVoteBtn.addEventListener('click', async () => {
                console.log('Clic détecté sur le bouton "Recevoir le code"'); // <-- DEUXIÈME CONSOLE.LOG

                alpineScope.isLoading = true;
                alpineScope.errorMessage = '';

                const countryCode = document.getElementById('country_code').value;
                const phoneDisplay = document.getElementById('telephone_display').value;
                document.getElementById('telephone_full').value = countryCode + phoneDisplay;

                grecaptcha.ready(function() {
                    grecaptcha.execute(otpRequestForm.dataset.recaptchaKey, { action: 'vote' })
                        .then(async function(token) {
                            document.getElementById('recaptcha-token').value = token;

                            const formData = new FormData(otpRequestForm);
                            const url = otpRequestForm.dataset.sendOtpUrl;
                            console.log('Numéro complet:', document.getElementById('telephone_full').value);


                            try {
                                const response = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                        'Accept': 'application/json',
                                    },
                                    body: formData
                                });

                                const data = await response.json();
                                if (!response.ok) throw new Error(data.message || 'Erreur serveur.');

                                if (data.success) {
                                    alpineScope.voteStep = 2;
                                }
                            } catch (error) {
                                alpineScope.errorMessage = error.message;
                            } finally {
                                alpineScope.isLoading = false;
                            }
                        });
                });
            });

            // Validation OTP
            submitOtpBtn.addEventListener('click', async () => {
                alpineScope.isLoading = true;
                alpineScope.errorMessage = '';

                const formData = new FormData(otpVerifyForm);
                const url = otpVerifyForm.dataset.verifyOtpUrl;
                console.log('Numéro complet:', document.getElementById('telephone_full').value);


                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Erreur lors de la vérification.');

                    if (data.success) {
                        alpineScope.successMessage = data.message;
                        alpineScope.voteStep = 3;
                    }
                } catch (error) {
                    alpineScope.errorMessage = error.message;
                    if (error.message.includes('expiré') || error.message.includes('invalide')) {
                        alpineScope.voteStep = 3;
                    }
                } finally {
                    alpineScope.isLoading = false;
                }
            });

        });
    </script>

    <!-- Police -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { 
                    poppins: [
                        'Poppins', 
                        'Segoe UI Emoji', 
                        'Apple Color Emoji', 
                        'Noto Color Emoji', 
                        'sans-serif'
                    ] 
                },
            },
        },
    }
</script>


    <style>
        html, body {
            background-color: #000000; /* Noir pur */
        }
        .bg-image-custom {
            background-image: url('{{ asset('images/LogoBG.jpg') }}');
        }
    </style>
    <style>
        /* Style personnalisé pour les boutons avec effet skew */
        .skew-btn {
            position: relative;
            display: inline-block;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.5s;
            z-index: 1;
            border-radius: 6px;
            overflow: hidden;
        }

        .skew-btn::before {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 100%;
            left: 0;
            background: rgb(20, 20, 20);
            opacity: 0;
            z-index: -1;
            content: '';
            transition: all 0.5s;
        }

        .skew-btn:hover::before {
            left: 0;
            right: 0;
            opacity: 1;
        }
        /* Pour Firefox */
.scrollbar-thin {
    scrollbar-width: thin;          /* rend la barre plus fine */
    scrollbar-color: #facc15 #1f2937; /* couleur : thumb / track */
}

/* Pour Chrome, Edge, Safari */
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;   /* largeur de la scrollbar */
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #1f2937;  /* couleur de la piste */
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: #facc15; /* couleur du “thumb” */
    border-radius: 10px;
    border: 2px solid #1f2937; /* espace autour */
}

    </style>
</head>
<body class="bg-black text-white flex flex-col min-h-screen bg-cover bg-center bg-fixed bg-image-custom font-poppins">

    <x-header />

    <main class="flex-grow container mx-auto px-4 py-12 flex items-center">
        <div class="bg-black bg-opacity-60 p-8 rounded-lg shadow-2xl max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-4 text-yellow-400">Choisissez un projet</h1>
            <p class="text-center text-gray-300 mb-8">Recherchez un projet, une équipe ou un secteur, puis votez pour votre préféré.</p>

            <!-- Barre de recherche -->
            <div class="mb-8">
            <div class="mb-8">
                <form action="{{ route('vote.secteurs') }}" method="GET">
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
            <div x-data="{
                    showModal: false,
                    modalProjet: null,
                    showVoteModal: false,
                    voteProjet: null,
                    voteStep: 1,
                    isLoading: false,
                    errorMessage: '',
                    successMessage: '',
                    descriptionExpanded: false 
                 }" @keydown.escape.window="showModal = false; showVoteModal = false">

                {{-- Message "aucun résultat" géré par JS --}}
                <div id="no-results-message" class="text-center py-12" style="display: none;">
                    <p class="text-xl text-gray-400">Aucun secteur ou projet trouvé.</p>
                    <a href="{{ route('vote.secteurs') }}" class="mt-4 inline-block text-yellow-400 hover:text-yellow-300">
                        &larr; Revenir à la liste complète
                    </a>
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
                                            <div class="flex flex-col md:flex-row items-center justify-center gap-2">

                                                <!-- Bouton Détails -->
                                                <button 
                                                    type="button"
                                                    class="skew-btn w-full md:w-auto bg-gray-600 text-white hover:text-white"
                                                    @click="modalProjet = @js($projet); showModal = true; descriptionExpanded = false">
                                                    Détails
                                                </button>

                                                <!-- Bouton Voter -->
                                                <button
                                                    type="button"
                                                    class="skew-btn w-full md:w-auto bg-blue-800 text-white hover:text-white"
                                                    {{-- Réinitialise l'état de la modale à chaque ouverture --}}
                                                    @click="
                                                        voteProjet = @js($projet);
                                                        showVoteModal = true;
                                                        voteStep = 1; errorMessage = ''; successMessage = '';
                                                    ">
                                                    Voter
                                                </button>
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
                        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">

                        <div 
                            @click.away="showModal = false"
                            class="bg-gray-900/95 border border-yellow-400/30 rounded-lg shadow-2xl max-w-2xl w-full text-white relative flex flex-col"
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
                                <h2 class="text-2xl sm:text-3xl font-bold text-yellow-400 mb-2" x-text="modalProjet?.nom_projet"></h2>
                                <p class="text-md sm:text-lg text-gray-300">
                                    par <span class="font-semibold" x-text="modalProjet?.nom_equipe"></span>
                                </p>
                            </div>

                            <!-- Contenu -->
                            <div class="p-6 space-y-4 text-gray-200 overflow-y-auto scrollbar-thin">
                                <p><strong class="text-yellow-300">Résumé :</strong> 
                                    <span class="whitespace-pre-wrap" x-text="modalProjet?.resume"></span>
                                </p>
                                <div>
                                    <strong class="text-yellow-300">Description :</strong>
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
                    class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">

                    <div
                        @click.away="if (!isLoading) showVoteModal = false"
                        class="bg-gray-900/95 border border-yellow-400/30 rounded-lg shadow-2xl max-w-lg w-full text-white relative flex flex-col"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90">

                        <!-- En-tête -->
                        <div class="p-6 border-b border-gray-700 flex-shrink-0">
                            <button @click="if (!isLoading) showVoteModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl leading-none">&times;</button>
                            <h2 class="text-2xl font-bold text-yellow-400 mb-2">
                                Voter pour : <span x-text="voteProjet?.nom_projet"></span>
                            </h2>
                            <p class="text-gray-300">
                                Équipe : <span class="font-semibold" x-text="voteProjet?.nom_equipe"></span>
                            </p>
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
                                               class="w-full bg-gray-800 border border-gray-600 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                               placeholder="Ex: Paul David Mbaye">
                                    </div>

                                    <div>
                                        <label for="telephone_display" class="block mb-2 text-sm font-medium text-gray-300">Votre numéro de téléphone</label>
                                        <div class="flex">
                                            <select id="country_code" name="country_code" class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-center text-gray-200 bg-gray-700 border border-gray-600 rounded-l-lg hover:bg-gray-600 focus:ring-2 focus:outline-none focus:ring-yellow-400">
                                                @foreach($countries as $country)
                                                    <option value="{{ $country['dial_code'] }}" @if($country['code'] === 'SN') selected @endif>
                                                        {!! $country['flag'] !!} {{ $country['dial_code'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="relative w-full">
                                                <input type="tel" id="telephone_display" name="telephone_display"
                                                       class="block p-2.5 w-full z-20 text-sm text-white bg-gray-800 rounded-r-lg border-l-0 border border-gray-600 focus:ring-2 focus:outline-none focus:ring-yellow-400"
                                                       placeholder="" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-4">
                                        <button type="button" id="submit-vote-btn"
                                                class="w-full skew-btn bg-blue-800 text-white hover:text-white flex items-center justify-center"
                                                :disabled="isLoading">
                                            <span x-show="!isLoading">Recevoir le code de vote</span>
                                            <span x-show="isLoading">Envoi en cours...</span>
                                        </button>
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
                                               class="w-full bg-gray-800 border border-gray-600 rounded-lg py-2 px-3 text-white text-center text-2xl tracking-[1em]"
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
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</html>