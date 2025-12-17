<!DOCTYPE html>
<html lang="fr" class="overflow-x-hidden">
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
<body class="bg-black text-white flex flex-col min-h-screen bg-cover bg-center bg-fixed bg-image-custom font-poppins overflow-x-hidden">

    <x-header />

    <main class="flex-grow container mx-auto px-4 py-12 flex items-center overflow-x-hidden">
        <div class="bg-black bg-opacity-60 p-8 rounded-lg shadow-2xl max-w-6xl mx-auto w-full">
            <!-- Bouton Retour à l'accueil -->
            <div class="mb-4">
                <a href="{{ route('vote.index') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-yellow-400 transition-colors text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour
                </a>
            </div>

            <div class="text-center mb-4 px-2">
                <div x-data="{ open: false }" class="relative inline-block text-left max-w-full">
                    <div>
                        <button @click="open = !open" type="button" class="inline-flex flex-wrap justify-center items-center w-full rounded-md px-3 py-2 text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-yellow-400 hover:text-yellow-300 focus:outline-none gap-1" id="menu-button" aria-expanded="open" aria-haspopup="true">
                            <span class="whitespace-nowrap">Categorie :</span>
                            <span class="text-white break-words">{{ $categorie->nom }}</span>
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 md:h-6 md:w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
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
                         class="origin-top-right absolute left-1/2 -translate-x-1/2 sm:left-auto sm:right-0 sm:translate-x-0 mt-2 w-64 sm:w-56 rounded-md shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                         role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="py-1" role="none">
                            @foreach($allCategories->where('slug', '!=', $categorie->slug) as $cat)
                                <a href="{{ route('vote.secteurs', ['profile_type' => $cat->slug]) }}" class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 text-sm" role="menuitem" tabindex="-1">{{ $cat->nom }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-center text-gray-300 mb-8 px-4 text-sm sm:text-base">Recherchez un projet, une équipe , puis votez pour votre préféré.</p>

            <!-- Barre de recherche -->
            <div class="mb-8">
    <form action="{{ route('vote.secteurs', ['profile_type' => $categorie->slug]) }}" method="GET" class="w-full">
        <div class="flex flex-col sm:flex-row gap-2 w-full">
            <!-- Input + icône desktop -->
            <div class="relative flex-1 min-w-0">
                <input
                    type="text"
                    id="search-input"
                    name="search"
                    placeholder="Rechercher un projet, une équipe ..."
                    class="w-full bg-gray-900/50 border border-gray-700 rounded-lg py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
                <!-- Icône loupe cliquable sur desktop -->
                <button
                    type="submit"
                    class="hidden md:flex items-center justify-center absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-yellow-400 transition-colors"
                    aria-label="Lancer la recherche"
                >
                    <svg id="search-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                <!-- Spinner retiré -->
            </div>

            <!-- Bouton mobile retiré: la recherche est automatique -->
        </div>
    </form>
</div>


            <!-- Conteneur Alpine.js global -->
            {{--  Étape 1.1: Ajout des états pour la modale de vote (voteStep, messages, etc.) --}}
                <div
    x-data="{
        showModal: false,
        modalProjet: null,
        showVoteModal: false,
        voteProjet: null,

        //  Variables pour le statut du vote
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
        // Capturer le contexte Alpine pour les event listeners
        const self = this;

        // Si le vote est inactif, on initialise la modale
        if (!isVoteActive) {
            voteStep = 3;
            errorMessage = inactiveMessage;
        }
        // Écouteurs pour charger les projets depuis le client sans embarquer l'objet complet dans la page
        window.addEventListener('project-data', function(e) {
            console.log('📊 Alpine: Event project-data reçu', e.detail);
            self.modalProjet = e.detail;
            self.showModal = true;
            self.descriptionExpanded = false;
        });
        window.addEventListener('project-for-vote', function(e) {
            console.log('🎯 Alpine: Event project-for-vote reçu', e.detail);
            self.voteProjet = e.detail;
            self.showVoteModal = true;
            console.log('🎯 Alpine: showVoteModal mis à true');
            self.voteStep = isVoteActive ? 1 : 3;
            self.errorMessage = isVoteActive ? '' : inactiveMessage;
            self.successMessage = '';
            console.log('🎯 Alpine: voteStep =', self.voteStep, ', showVoteModal =', self.showVoteModal);
        });
    "

    @keydown.escape.window="showModal = false; showVoteModal = false"
>



                {{-- Message "aucun résultat" : s'affiche si une recherche est effectuée et qu'aucun projet n'a été rendu --}}
                @php
                    $__projectsCount = 0;
                    foreach ($secteurs as $__s) {
                        $__projectsCount += $__s->projets->count();
                    }
                @endphp

                @if(request('search') && $__projectsCount === 0)
                    <div id="no-results-message" class="text-center py-12">
                        <p class="text-xl text-gray-400">Aucun secteur ou projet trouvé.</p>
                        <a href="{{ route('vote.secteurs', ['profile_type' => $categorie->slug]) }}" class="mt-4 inline-block text-yellow-400 hover:text-yellow-300">
                            &larr; Revenir à la liste complète
                        </a>
                    </div>
                @else
                    <div id="no-results-message" class="text-center py-12" style="display: none;">
                        <p class="text-xl text-gray-400">Aucun secteur ou projet trouvé.</p>
                        <a href="{{ route('vote.secteurs', ['profile_type' => $categorie->slug]) }}" class="mt-4 inline-block text-yellow-400 hover:text-yellow-300">
                            &larr; Revenir à la liste complète
                        </a>
                    </div>
                @endif

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
                <div class="overflow-x-visible md:overflow-x-auto">
                    <table class="w-full text-left border-collapse md:max-w-4xl md:mx-auto">
                        <thead class="bg-gray-800 hidden md:table-header-group">
                            <tr>
                                <th class="p-4 text-lg font-semibold text-gray-100">Secteur</th>
                                <th class="p-4 text-lg font-semibold text-gray-100">Equipe</th>
                                <th class="p-4 text-lg font-semibold text-gray-100">Projet</th>
                                <th class="p-4 text-lg font-semibold text-gray-100 text-center">Vote</th>
                            </tr>
                        </thead>
                        <tbody id="projects-table-body">
                            @foreach ($secteurs as $secteur)
                                @forelse ($secteur->projets as $projet)
                                    @php
                                        // Extraire l'école une seule fois pour éviter la duplication
                                        $school = null;
                                        if ($projet->submission?->profile_type === 'student' && $projet->listePreselectionne?->snapshot) {
                                            $snapshot = json_decode($projet->listePreselectionne->snapshot, true);
                                            if (isset($snapshot['champs_personnalises'])) {
                                                $champsPerso = is_string($snapshot['champs_personnalises'])
                                                    ? json_decode($snapshot['champs_personnalises'], true)
                                                    : $snapshot['champs_personnalises'];

                                                // Si l'école est "OTHER", utiliser le champ student_school_other
                                                $schoolValue = $champsPerso['student_school'] ?? null;
                                                if ($schoolValue === 'OTHER') {
                                                    $school = $champsPerso['student_school_other'] ?? null;
                                                } else {
                                                    $school = $schoolValue;
                                                }
                                            }
                                        }
                                    @endphp
                                    <tr class="block md:table-row border-b border-gray-700 hover:bg-gray-900/30 transition-colors bg-gray-800/40 md:bg-transparent rounded-lg md:rounded-none mb-3 md:mb-0">
                                        <td class="hidden md:table-cell p-4" data-label="Secteur : ">{{ $secteur->nom }}</td>
                                        <td class="hidden md:table-cell p-4" data-label="Équipe : ">
                                            <div>{{ $projet->nom_equipe }}</div>
                                            @if($school)
                                                <div class="text-sm text-yellow-300 font-semibold mt-1">{{ $school }}</div>
                                            @endif
                                        </td>
                                        <td class="block md:table-cell p-3 md:p-4 font-semibold" data-label="Projet : ">
                                            <div class="flex flex-col gap-1">
                                                <div class="md:hidden">   <!-- Cache le bloc a partir d'un ecran mobile -->
                                                    <div class="text-[10px] text-gray-400 font-medium tracking-tight mb-1">Nom Équipe :</div>
                                                    <div class="text-sm text-white">{{ $projet->nom_equipe }}</div>
                                                    @if($school)
                                                        <div class="text-xs text-yellow-300 font-bold mt-1">{{ $school }}</div>
                                                    @endif
                                                    <div class="text-[10px] text-gray-400 font-medium tracking-tight mt-2 mb-1">Nom Projet :</div>
                                                    <div class="text-sm font-semibold text-white">{{ $projet->nom_projet }}</div>
                                                </div>
                                                <span class="hidden md:inline">{{ $projet->nom_projet }}</span>
                                            </div>
                                        </td>
                                        <td class="block md:table-cell p-3 md:p-4 text-center align-middle">
                                                   <!-- Mobile: row of icon buttons, then vote button below -->
                                                   <!-- Desktop: flex row as before -->
                                                   <div class="md:hidden flex flex-col gap-3">
                                                       <!-- Row of 3 icon buttons centered -->
                                                       <div class="flex items-center justify-center gap-3">
                                                           <!-- Bouton Détails -->
                                                           <button
                                                               type="button"
                                                               class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                               aria-label="Détails du projet"
                                                               title="Détails"
                                                               @click="modalProjet = @js($projet); showModal = true; descriptionExpanded = false">
                                                               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                                   <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                               </svg>
                                                           </button>

                                                           <!-- Bouton Partager -->
                                                           <button
                                                               type="button"
                                                               class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                               title="Partager ce projet"
                                                               onclick="shareProjectForProject({{ $projet->id }})">
                                                               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                   <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                                               </svg>
                                                           </button>

                                                           @php
                                                               $demoUrl = $projet->video_demo ?? $projet->video_demonstration ?? \Illuminate\Support\Facades\DB::table('liste_preselectionnes')->where('projet_id', $projet->id)->value('video_demo') ?? \Illuminate\Support\Facades\DB::table('liste_preselectionnes')->where('projet_id', $projet->id)->value('video_demonstration');
                                                           @endphp
                                                           @if($demoUrl)
                                                               <a href="{{ $demoUrl }}" target="_blank" rel="noopener noreferrer"
                                                                  class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors" title="Voir la démonstration">
                                                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                                       <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
                                                                       <rect x="2" y="5" width="11" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                   </svg>
                                                               </a>
                                                           @else
                                                               <span class="p-2 rounded-full text-gray-600 bg-transparent opacity-60" title="Aucune démonstration disponible" aria-hidden="true">
                                                                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                                                                       <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
                                                                       <rect x="2" y="5" width="11" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                       <line x1="3" y1="3" x2="21" y2="21" stroke-linecap="round" stroke-linejoin="round" />
                                                                   </svg>
                                                               </span>
                                                           @endif
                                                       </div>
                                                       <!-- Vote button below, full width -->
                                                       <div class="relative">
                                                           <button
                                                               data-role="vote-btn"
                                                               type="button"
                                                               class="group flex items-center justify-center gap-2 w-full px-4 py-2 text-sm font-bold rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-yellow-400/20"
                                                               :class="{
                                                                   'bg-green-400/75 text-gray-100 hover:bg-yellow-300 hover:text-black': isVoteActive,
                                                                   'bg-gray-600 text-gray-300 cursor-not-allowed': !isVoteActive
                                                               }"
                                                               :disabled="!isVoteActive"
                                                               @click="voteProjet = @js($projet); showVoteModal = true; voteStep = isVoteActive ? 1 : 3; errorMessage = isVoteActive ? '' : inactiveMessage; successMessage = '';">
                                                               <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                   <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                               </svg>
                                                               Voter
                                                           </button>
                                                           <button x-show="!isVoteActive" @click.prevent="showInactiveNotice()" class="absolute inset-0 w-full h-full z-20 bg-transparent" aria-hidden="true"></button>
                                                       </div>
                                                   </div>

                                                   <!-- Desktop: original flex row layout -->
                                                   <div class="hidden md:flex relative md:flex-row items-center justify-center gap-2">
                                                       <!-- Bouton Détails (icône seulement) -->
                                                       <button
                                                           type="button"
                                                           class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                           aria-label="Détails du projet"
                                                           title="Détails"
                                                           @click="modalProjet = @js($projet); showModal = true; descriptionExpanded = false">
                                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                               <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                               <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                           </svg>
                                                       </button>

                                                       <!-- Bouton Partager -->
                                                       <button
                                                           type="button"
                                                           class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                           title="Partager ce projet"
                                                           onclick="shareProjectForProject({{ $projet->id }})">
                                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                               <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                                           </svg>
                                                       </button>

                                                       @php
                                                           $demoUrl = $projet->video_demo ?? $projet->video_demonstration ?? \Illuminate\Support\Facades\DB::table('liste_preselectionnes')->where('projet_id', $projet->id)->value('video_demo') ?? \Illuminate\Support\Facades\DB::table('liste_preselectionnes')->where('projet_id', $projet->id)->value('video_demonstration');
                                                       @endphp
                                                       @if($demoUrl)
                                                           <a href="{{ $demoUrl }}" target="_blank" rel="noopener noreferrer"
                                                              class="p-2 ml-1 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors" title="Voir la démonstration">
                                                               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                                   <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
                                                                   <rect x="2" y="5" width="11" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" />
                                                               </svg>
                                                           </a>
                                                       @else
                                                           <span class="p-2 ml-1 rounded-full text-gray-600 bg-transparent opacity-60" title="Aucune démonstration disponible" aria-hidden="true">
                                                               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                                                                   <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
                                                                   <rect x="2" y="5" width="11" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                   <line x1="3" y1="3" x2="21" y2="21" stroke-linecap="round" stroke-linejoin="round" />
                                                               </svg>
                                                           </span>
                                                       @endif

                                                       <!-- Bouton Voter () -->
                                                       <button
                                                           data-role="vote-btn"
                                                           type="button"
                                                           class="group flex items-center justify-center gap-2 md:w-auto px-4 py-2 text-sm font-bold rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-yellow-400/20"
                                                           :class="{
                                                               'bg-green-400/75 text-gray-100 hover:bg-yellow-300 hover:text-black': isVoteActive,
                                                               'bg-gray-600 text-gray-300 cursor-not-allowed': !isVoteActive
                                                           }"
                                                           :disabled="!isVoteActive"
                                                           @click="voteProjet = @js($projet); showVoteModal = true; voteStep = isVoteActive ? 1 : 3; errorMessage = isVoteActive ? '' : inactiveMessage; successMessage = '';">
                                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                               <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                           </svg>
                                                           Voter
                                                       </button>

                                                       <!-- Overlay pour capter le clic quand le vote est INactif (au-dessus du bouton Voter) -->
                                                       <button x-show="!isVoteActive" @click.prevent="showInactiveNotice()" class="absolute inset-0 w-full h-full z-20 bg-transparent" aria-hidden="true"></button>
                                                   </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            @endforeach
                        </tbody>
                    </table>

                </div>
                <div class="mt-6">

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

                            {{-- Étape 1: Formulaire Nom & Téléphone --}}
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
                                        <input type="text" id="nom_votant" name="nom_votant"
                                               class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                               placeholder="Ex: Paul David Mbaye">
                                    </div>

                                    <div>
                                        <label for="telephone_display" class="block mb-2 text-sm font-medium text-gray-300">Votre numéro de téléphone</label>
                                        <div class="flex flex-row gap-0">
                                            <select id="country_code" name="country_code" class="hidden">
                                                <option value="+221" selected>+221</option>
                                            </select>
                                            <div class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-3 text-sm font-medium text-center text-gray-200 bg-gray-800 border border-gray-600 rounded-l-lg">
                                                🇸🇳 +221
                                            </div>
                                            <div class="relative w-full">
                                                <input type="tel" id="telephone_display" name="telephone_display"
                                                       class="block p-2.5 w-full z-20 text-sm text-white bg-gray-700/50 rounded-r-lg border border-gray-600 border-l-0 focus:ring-2 focus:outline-none focus:ring-yellow-400"
                                                       placeholder="Ex: 77 123 45 67" required>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-400">Vous recevrez un code unique par SMS pour valider votre vote.</p>
                                    </div>

                                    <div class="pt-4 flex justify-center">
                                        <div class="rainbow relative z-0 overflow-hidden p-0.5 flex items-center justify-center rounded-full hover:scale-105 transition duration-300 active:scale-100 w-full">
                                            <button type="button" id="submit-vote-btn"
                                                    class="w-full px-8 text-sm py-3 text-white rounded-full font-medium bg-gray-900 flex items-center justify-center"
                                                    :disabled="isLoading">
                                                <span x-show="!isLoading">Cliquez pour recevoir le code de vote</span>
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
                                        <input type="tel" id="otp" name="otp"
                                               class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-2 px-3 text-white text-center text-2xl tracking-[1em]"
                                               placeholder="------" required maxlength="6" pattern="\d{6}" inputmode="numeric">
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

                            {{-- Étape 3: Messages de succès ou d'erreur --}}
                            <div x-show="voteStep === 3" style="display: none;" class="text-center py-6 px-4">
                                {{-- Message de succès --}}
                                <div x-show="successMessage" class="space-y-5">
                                    {{-- Icône et message principal - version compacte --}}
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                                        <div class="w-16 h-16 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-8 h-8 sm:w-7 sm:h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div class="text-center sm:text-left">
                                            <h3 class="text-xl sm:text-2xl font-semibold text-white mb-1">Vote enregistré</h3>
                                            <p class="text-sm text-gray-400">Merci pour votre participation</p>
                                        </div>
                                    </div>

                                    {{-- Invitation grande finale - version compacte --}}
                                    <div class="pt-4 pb-2 border-t border-gray-800">
                                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 max-w-lg mx-auto">
                                            <div class="text-center sm:text-left flex-1">
                                                <h4 class="text-base font-medium text-white mb-1">Grande Finale</h4>
                                                <p class="text-xs text-gray-400">Réservez votre place dès maintenant</p>
                                            </div>
                                            <a href="https://reservation.govathon.sn"
                                               target="_blank"
                                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-black font-medium text-sm rounded-lg transition-all duration-200 shadow-lg hover:shadow-yellow-500/20 flex-shrink-0">
                                                <span>Réserver</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Bouton fermer intégré --}}
                                    <button @click="showVoteModal = false" class="text-sm text-gray-500 hover:text-gray-300 underline transition-colors">
                                        Fermer
                                    </button>
                                </div>

                                {{-- Message d'erreur - version compacte --}}
                                <div x-show="errorMessage" class="space-y-4">
                                    <div class="flex items-center justify-center gap-4">
                                        <div class="w-14 h-14 rounded-full bg-red-500/10 border border-red-500/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                        <p class="text-base text-gray-300 text-left flex-1" x-text="errorMessage"></p>
                                    </div>
                                    <button @click="showVoteModal = false" class="text-sm text-gray-500 hover:text-gray-300 underline transition-colors">
                                        Fermer
                                    </button>
                                </div>
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

<script>
window.shareProjectForProject = function (id) {
    console.log('🔵 shareProjectForProject appelé avec ID:', id);

    const urlObj = new URL(window.location.href);
    urlObj.searchParams.set('vote', '1');       // flag spécial pour ouvrir la popup de vote
    urlObj.searchParams.set('project_id', id);  // id du projet

    const finalUrl = urlObj.toString();
    console.log('🔵 URL générée pour le partage:', finalUrl);

    // Si window.shareProject vient de app.js, on l'utilise
    if (typeof window.shareProject === 'function') {
        window.shareProject(finalUrl);
    } else {
        // Fallback simple
        const title = document.title || 'Découvrez ce projet';
        const text  = 'Jetez un œil à ce projet et votez pour lui :';

        if (navigator.share) {
            navigator.share({ title, text, url: finalUrl }).catch(() => {});
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(finalUrl)
                .then(() => alert('Lien copié dans le presse-papiers'))
                .catch(() => prompt('Copiez ce lien :', finalUrl));
            return;
        }

        prompt('Copiez ce lien :', finalUrl);
    }
};
</script>

<script>
(function () {
    let voteDeepLinkHandled = false;

    function openVoteFromUrl() {
        if (voteDeepLinkHandled) return;
        voteDeepLinkHandled = true;

        const params = new URLSearchParams(window.location.search);
        const wantVote  = params.get('vote') === '1';
        const projectId = params.get('project_id');

        if (!wantVote || !projectId) {
            console.log('🔴 Pas de paramètres vote=1 & project_id, rien à ouvrir.');
            return;
        }

        console.log('🟡 Ouverture automatique de la modale de vote pour le projet', projectId);

        fetch('/vote/project/' + projectId + '/data')
            .then(function (res) {
                console.log('🟡 Réponse fetch auto-vote:', res.status, res.ok);
                if (!res.ok) throw new Error('Impossible de charger le projet');
                return res.json();
            })
            .then(function (data) {
                console.log('✅ Données projet reçues pour auto-vote :', data);

                // ➜ Ici on déclenche l’event attendu par Alpine
                window.dispatchEvent(new CustomEvent('project-for-vote', { detail: data }));

                // Nettoyage de l'URL pour éviter la réouverture au refresh
                if (history && history.replaceState) {
                    const cleanUrl = new URL(window.location);
                    cleanUrl.searchParams.delete('vote');
                    cleanUrl.searchParams.delete('project_id');
                    history.replaceState({}, '', cleanUrl.toString());
                    console.log('✅ URL nettoyée après ouverture de la modale de vote');
                }
            })
            .catch(function (err) {
                console.error('❌ Erreur auto-vote / fetch projet :', err);
            });
    }

    // Quand Alpine a fini d'initialiser les composants
    document.addEventListener('alpine:initialized', function () {
        console.log('🎉 alpine:initialized (auto-vote)');
        openVoteFromUrl();
    });

    // Fallback au cas où l'événement passe à côté
    window.addEventListener('load', function () {
        setTimeout(openVoteFromUrl, 400);
    });
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-input');
    if (!input) return;

    const form = input.closest('form');
    if (!form) return;

    let timer = null;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        // lance la recherche 400 ms après que l'utilisateur a fini de taper
        //sd
        timer = setTimeout(() => form.submit(), 400);
    });
});
</script>

