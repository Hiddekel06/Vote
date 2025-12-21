{{-- resources/views/vote-jour-j.blade.php --}}
<!DOCTYPE html>
<html lang="fr" class="overflow-x-hidden">
<head>
    <!-- Google tag (gtag.js) - Analytics -->
    @if(config('app.google_analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('app.google_analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('app.google_analytics_id') }}');
        </script>
    @endif

    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grande Finale Jour J - GovAthon</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Police -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Si Alpine n'est pas déjà dans ton bundle Vite, décommente ceci -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        .bg-image-custom { background-image: url('{{ asset('assets/img/bg-vote.jpg') }}'); }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-black text-white flex flex-col min-h-screen bg-cover bg-center bg-fixed bg-image-custom font-poppins overflow-x-hidden">

<x-header />

@php
    // Flags simples pour la vue
    $isVoteActive = $isVoteActive ?? true;
    $inactiveMessage = $inactiveMessage ?? "Le vote Jour J n'est pas ouvert pour le moment.";

    $hasEvent = (bool) $event;
    $eventPayload = $event ? [
        'lat' => (float) $event->latitude,
        'lon' => (float) $event->longitude,
        'radius' => (int) $event->rayon_metres,
    ] : null;

    $__projectsCount = 0;
    foreach ($secteurs as $__s) {
        $__projectsCount += $__s->projets->count();
    }
@endphp

<main class="flex-grow container mx-auto px-4 py-12 flex items-center overflow-x-hidden">
    <div
        class="bg-black bg-opacity-60 p-8 rounded-lg shadow-2xl max-w-6xl mx-auto w-full"
        x-data="voteJourJComponent({
            isVoteActive: @json($isVoteActive),
            inactiveMessage: @json($inactiveMessage),
            selectedCategory: @json(request('profile_type', 'all')),
            hasEvent: @json($hasEvent),
            event: @json($eventPayload),
            recaptchaKey: @json(config('services.recaptcha.site_key')),
            sendOtpUrl: @json(route('vote-jour-j.envoyerOtp')),
            verifyOtpUrl: @json(route('vote-jour-j.verifierOtp')),
        })"
        x-init="init()"
        @keydown.escape.window="closeAll()"
    >

        <!-- Toast rapide quand vote désactivé -->
        <div
            x-cloak
            x-show="inactiveNoticeVisible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed top-4 inset-x-0 flex justify-center z-50 px-4"
        >
            <div class="bg-red-900/90 border border-red-500/60 text-red-100 text-sm px-4 py-2 rounded-full shadow-lg">
                Le vote Jour J est momentanément fermé.
            </div>
        </div>

        <!-- Modale d'accueil - Étapes du vote -->
        <div
            x-cloak
            class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center z-50 p-4"
            x-show="showWelcomeModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <div
                class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-yellow-500/20 p-8 text-center"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
            >
                <h1 class="text-3xl font-bold text-yellow-400 mb-8">🏆 GRANDE FINALE<br>GOV'ATHON</h1>

                <div class="space-y-4 mb-8 text-left">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-black rounded-full font-bold flex items-center justify-center">1</div>
                        <div>
                            <p class="text-white font-semibold">Activer la localisation</p>
                            <p class="text-gray-400 text-sm">Nous aurons besoin de votre position GPS</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-black rounded-full font-bold flex items-center justify-center">2</div>
                        <div>
                            <p class="text-white font-semibold">Sélectionner un projet</p>
                            <p class="text-gray-400 text-sm">Parcourez et choisissez votre favori</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-black rounded-full font-bold flex items-center justify-center">3</div>
                        <div>
                            <p class="text-white font-semibold">Entrer votre numéro</p>
                            <p class="text-gray-400 text-sm">Pour vérifier votre identité</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-yellow-500 text-black rounded-full font-bold flex items-center justify-center">4</div>
                        <div>
                            <p class="text-white font-semibold">Confirmer votre vote</p>
                            <p class="text-gray-400 text-sm">Validez et c'est fait !</p>
                        </div>
                    </div>
                </div>

                <button
                    @click="startFromWelcome()"
                    class="w-full px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-lg font-bold transition-colors"
                >
                    Commencer
                </button>
            </div>
        </div>

        <!-- Bouton Retour -->
        <div class="mb-4">
            <a href="{{ route('vote.index') }}"
               class="inline-flex items-center gap-2 text-gray-400 hover:text-yellow-400 transition-colors text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                          clip-rule="evenodd" />
                </svg>
                Retour
            </a>
        </div>

        <!-- Titre -->
        <div class="text-center mb-4 px-2">
            <h1 class="text-3xl md:text-4xl font-bold text-yellow-400 mb-2">🏆 GRANDE FINALE JOUR J</h1>

            @if(!$event)
                <div class="mt-2 px-4 py-2 bg-red-900/30 border border-red-700 rounded-lg inline-block">
                    <p class="text-red-300 text-xs">❌ Le vote Jour J est actuellement désactivé</p>
                </div>
            @endif

            <div class="mt-2 text-[11px] text-gray-400">
                ⚠️ La géolocalisation fonctionne uniquement sur <strong>HTTPS</strong> (ou <strong>localhost</strong>) et certains lecteurs QR ouvrent un navigateur interne sans GPS.
            </div>
        </div>

        <!-- 🌍 Indicateur GPS -->
        <div class="mb-6 px-2">
            <div class="max-w-2xl mx-auto">

                <!-- IDLE -->
                <div
                    x-cloak
                    x-show="gpsStatus === 'idle'"
                    class="flex flex-col items-center justify-center gap-2 bg-gray-800/50 border border-gray-600/50 rounded-lg px-4 py-3"
                >
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"></path>
                        </svg>
                        <span class="text-gray-200 text-sm font-medium" x-text="gpsMessage"></span>
                    </div>
                    <button @click="captureGPS(true)" class="text-xs text-yellow-300 hover:text-yellow-200 underline">
                        Activer la localisation
                    </button>
                </div>

                <!-- Loading -->
                <div
                    x-cloak
                    x-show="gpsStatus === 'loading'"
                    class="flex items-center justify-center gap-3 bg-blue-900/30 border border-blue-600/50 rounded-lg px-4 py-3"
                >
                    <svg class="animate-spin h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-blue-300 text-sm font-medium" x-text="gpsMessage"></span>
                </div>

                <!-- Succès -->
                <div
                    x-cloak
                    x-show="gpsStatus === 'success' && isInRange"
                    class="flex items-center justify-center gap-3 bg-green-900/30 border border-green-500/50 rounded-lg px-4 py-3"
                >
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-300 text-sm font-medium" x-text="gpsMessage"></span>
                </div>

                <!-- Hors du rayon / Erreur -->
                <div
                    x-cloak
                    x-show="gpsStatus === 'error'"
                    class="flex flex-col items-center justify-center gap-2 bg-red-900/30 border border-red-500/50 rounded-lg px-4 py-3"
                >
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-red-300 text-sm font-medium" x-text="gpsMessage"></span>
                    </div>
                    <button @click="captureGPS(true)" class="text-xs text-red-200 hover:text-white underline">
                        Réessayer
                    </button>
                </div>

                <!-- Permission refusée -->
                <div
                    x-cloak
                    x-show="gpsStatus === 'denied'"
                    class="flex flex-col items-center justify-center gap-2 bg-orange-900/30 border border-orange-500/50 rounded-lg px-4 py-3"
                >
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span class="text-orange-300 text-sm font-medium" x-text="gpsMessage"></span>
                    </div>
                    <p class="text-xs text-orange-200 text-center">
                        Cliquez sur l'icône 🔒 dans votre navigateur pour autoriser la géolocalisation
                    </p>
                    <button @click="captureGPS(true)" class="text-xs text-orange-200 hover:text-white underline">
                        Réessayer
                    </button>
                </div>

                <!-- Non disponible -->
                <div
                    x-cloak
                    x-show="gpsStatus === 'unavailable'"
                    class="flex items-center justify-center gap-3 bg-gray-800/50 border border-gray-600/50 rounded-lg px-4 py-3"
                >
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path>
                    </svg>
                    <span class="text-gray-300 text-sm font-medium" x-text="gpsMessage"></span>
                </div>

            </div>
        </div>

        <!-- Dropdown catégories -->
        <div class="text-center mb-4 px-2">
            <div class="relative inline-block text-left max-w-full">
                <button
                    @click="categoryMenuOpen = !categoryMenuOpen"
                    type="button"
                    class="inline-flex flex-wrap justify-center items-center w-full rounded-md px-3 py-2 text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-yellow-400 hover:text-yellow-300 focus:outline-none gap-1"
                    aria-haspopup="true"
                    :aria-expanded="categoryMenuOpen"
                >
                    <span class="whitespace-nowrap">Catégorie :</span>
                    <span class="text-white break-words"
                          x-text="selectedCategory === 'all'
                            ? 'Toutes'
                            : (selectedCategory === 'student'
                                ? 'Étudiant'
                                : (selectedCategory === 'startup'
                                    ? 'Startup'
                                    : 'Porteurs de projet'))"></span>
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 md:h-6 md:w-6 flex-shrink-0"
                         xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20"
                         fill="currentColor"
                         aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd" />
                    </svg>
                </button>

                <div
                    x-cloak
                    x-show="categoryMenuOpen"
                    @click.away="categoryMenuOpen = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="origin-top-right absolute left-1/2 -translate-x-1/2 sm:left-auto sm:right-0 sm:translate-x-0 mt-2 w-64 sm:w-56 rounded-md shadow-lg bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                    role="menu"
                    aria-orientation="vertical"
                    tabindex="-1"
                >
                    <div class="py-1" role="none">
                        <a href="{{ route('vote-jour-j.show', request('search') ? ['search' => request('search')] : []) }}"
                           class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 text-sm w-full text-left"
                           role="menuitem">
                            Toutes les catégories
                        </a>
                        <a href="{{ route('vote-jour-j.show', array_filter(['profile_type' => 'student', 'search' => request('search')])) }}"
                           class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 text-sm w-full text-left"
                           role="menuitem">
                            Étudiant
                        </a>
                        <a href="{{ route('vote-jour-j.show', array_filter(['profile_type' => 'startup', 'search' => request('search')])) }}"
                           class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 text-sm w-full text-left"
                           role="menuitem">
                            Startup
                        </a>
                        <a href="{{ route('vote-jour-j.show', array_filter(['profile_type' => 'other', 'search' => request('search')])) }}"
                           class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 text-sm w-full text-left"
                           role="menuitem">
                            Porteurs de projet
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center text-gray-300 mb-8 px-4 text-sm sm:text-base">
            Recherchez un projet, une équipe, puis votez pour votre préféré.
            <span class="text-gray-500">({{ $__projectsCount }} projet(s))</span>
        </p>

        <!-- Barre de recherche -->
        <div class="mb-8">
            <form action="{{ route('vote-jour-j.show') }}" method="GET" class="w-full">
                <div class="flex flex-col sm:flex-row gap-2 w-full">
                    @if(request('profile_type'))
                        <input type="hidden" name="profile_type" value="{{ request('profile_type') }}">
                    @endif

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
                        <button
                            type="submit"
                            class="hidden md:flex items-center justify-center absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-yellow-400 transition-colors"
                            aria-label="Lancer la recherche"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
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
                            $school = null;
                            if ($projet->submission?->profile_type === 'student' && $projet->listePreselectionne?->snapshot) {
                                $snapshot = json_decode($projet->listePreselectionne->snapshot, true);
                                if (isset($snapshot['champs_personnalises'])) {
                                    $champsPerso = is_string($snapshot['champs_personnalises'])
                                        ? json_decode($snapshot['champs_personnalises'], true)
                                        : $snapshot['champs_personnalises'];

                                    $schoolValue = $champsPerso['student_school'] ?? null;
                                    $school = ($schoolValue === 'OTHER')
                                        ? ($champsPerso['student_school_other'] ?? null)
                                        : $schoolValue;
                                }
                            }

                            $profileType = $projet->submission?->profile_type ?? 'other';

                            $demoUrl = $projet->video_demo
                                ?? $projet->video_demonstration
                                ?? \Illuminate\Support\Facades\DB::table('liste_preselectionnes')->where('projet_id', $projet->id)->value('video_demo')
                                ?? \Illuminate\Support\Facades\DB::table('liste_preselectionnes')->where('projet_id', $projet->id)->value('video_demonstration');
                        @endphp

                        <tr
                            class="block md:table-row border-b border-gray-700 hover:bg-gray-900/30 transition-colors bg-gray-800/40 md:bg-transparent rounded-lg md:rounded-none mb-3 md:mb-0"
                            :class="{ 'opacity-50 hover:bg-gray-800/20': !hasEvent }"
                            x-show="selectedCategory === 'all' || selectedCategory === '{{ $profileType }}'"
                        >
                            <td class="hidden md:table-cell p-4" data-label="Secteur : ">{{ $secteur->nom }}</td>

                            <td class="hidden md:table-cell p-4" data-label="Équipe : ">
                                <div>{{ $projet->nom_equipe }}</div>
                                @if($school)
                                    <div class="text-sm text-yellow-300 font-semibold mt-1">{{ $school }}</div>
                                @endif
                            </td>

                            <td class="block md:table-cell p-3 md:p-4 font-semibold" data-label="Projet : ">
                                <div class="flex flex-col gap-1">
                                    <div class="md:hidden">
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
                                <!-- Mobile -->
                                <div class="md:hidden flex flex-col gap-3">
                                    <div class="flex items-center justify-center gap-3">
                                        <!-- Détails -->
                                        <button
                                            type="button"
                                            class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                            title="Détails"
                                            @click="openDetails(@js($projet))"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <!-- Partager -->
                                        <button
                                            type="button"
                                            class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                            title="Partager ce projet"
                                            onclick="shareProjectForProject({{ $projet->id }}, @js($projet->nom_projet))"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                            </svg>
                                        </button>

                                        <!-- Démo -->
                                        @if($demoUrl)
                                            <a href="{{ $demoUrl }}" target="_blank" rel="noopener noreferrer"
                                               class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                               title="Voir la démonstration">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                                     fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
                                                    <rect x="2" y="5" width="11" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="p-2 rounded-full text-gray-600 bg-transparent opacity-60"
                                                  title="Aucune démonstration disponible" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                                     fill="none" stroke="currentColor" stroke-width="1.2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
                                                    <rect x="2" y="5" width="11" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <line x1="3" y1="3" x2="21" y2="21" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Bouton voter (mobile) -->
                                    <div class="relative">
                                        <button
                                            type="button"
                                            class="group flex items-center justify-center gap-2 w-full px-4 py-2 text-sm font-bold rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-yellow-400/20"
                                            :class="voteButtonClass()"
                                            :disabled="voteButtonDisabled()"
                                            @click="openVote(@js($projet))"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Voter
                                        </button>

                                        <button
                                            x-cloak
                                            x-show="voteButtonDisabled()"
                                            @click.prevent="explainWhyVoteDisabled()"
                                            class="absolute inset-0 w-full h-full z-20 bg-transparent"
                                            aria-hidden="true"
                                        ></button>
                                    </div>
                                </div>

                                <!-- Desktop -->
                                <div class="hidden md:flex relative md:flex-row items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                        title="Détails"
                                        @click="openDetails(@js($projet))"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                        title="Partager ce projet"
                                        onclick="shareProjectForProject({{ $projet->id }}, @js($projet->nom_projet))"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                        </svg>
                                    </button>

                                    @if($demoUrl)
                                        <a href="{{ $demoUrl }}" target="_blank" rel="noopener noreferrer"
                                           class="p-2 ml-1 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                           title="Voir la démonstration">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
                                                <rect x="2" y="5" width="11" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="p-2 ml-1 rounded-full text-gray-600 bg-transparent opacity-60"
                                              title="Aucune démonstration disponible" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="1.2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
                                                <rect x="2" y="5" width="11" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <line x1="3" y1="3" x2="21" y2="21" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    @endif

                                    <button
                                        type="button"
                                        class="group flex items-center justify-center gap-2 md:w-auto px-4 py-2 text-sm font-bold rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-yellow-400/20"
                                        :class="voteButtonClass()"
                                        :disabled="voteButtonDisabled()"
                                        @click="openVote(@js($projet))"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Voter
                                    </button>

                                    <button
                                        x-cloak
                                        x-show="voteButtonDisabled()"
                                        @click.prevent="explainWhyVoteDisabled()"
                                        class="absolute inset-0 w-full h-full z-20 bg-transparent"
                                        aria-hidden="true"
                                    ></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modale DÉTAILS --}}
        <div
            x-cloak
            x-show="showModal"
            class="fixed inset-0 bg-black/5 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                @click.away="showModal = false"
                class="bg-gray-900/95 border-yellow-400/30 rounded-lg shadow-2xl max-w-2xl w-full text-white relative flex flex-col"
                style="max-height: 90vh;"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90"
            >
                <div class="p-6 border-b border-gray-700 flex-shrink-0">
                    <button @click="showModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl leading-none">
                        &times;
                    </button>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-400 mb-2" x-text="modalProjet?.nom_projet"></h2>
                    <p class="text-md sm:text-lg text-gray-300">
                        par <span class="font-semibold" x-text="modalProjet?.nom_equipe"></span>
                    </p>
                </div>

                <div class="p-6 space-y-4 text-gray-200 overflow-y-auto scrollbar-thin">
                    <p>
                        <strong class="text-gray-300">Résumé :</strong>
                        <span class="whitespace-pre-wrap" x-text="modalProjet?.resume"></span>
                    </p>

                    <div>
                        <strong class="text-gray-300">Description :</strong>
                        <div
                            class="whitespace-pre-wrap"
                            :class="{'max-h-24 overflow-hidden': !descriptionExpanded && (modalProjet?.description?.length || 0) > 250}"
                        >
                            <span x-text="modalProjet?.description"></span>
                        </div>

                        <button
                            x-cloak
                            x-show="(modalProjet?.description?.length || 0) > 250"
                            @click="descriptionExpanded = !descriptionExpanded"
                            class="text-yellow-400 hover:text-yellow-300 mt-2 text-sm"
                        >
                            <span x-text="descriptionExpanded ? 'Voir moins' : 'Voir plus'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modale VOTE --}}
        <div
            x-cloak
            x-show="showVoteModal"
            class="fixed inset-0 bg-black/5 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                @click.away="if (!isLoading) showVoteModal = false"
                class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg shadow-2xl max-w-lg w-full text-white relative flex flex-col"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90"
            >
                <div class="p-6 border-b border-gray-700 flex-shrink-0 relative">
                    <button
                        @click="if (!isLoading) showVoteModal = false"
                        class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors p-1 rounded-full hover:bg-gray-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <h2 class="text-2xl font-bold text-yellow-400 mb-2">
                        Voter pour : <span x-text="voteProjet?.nom_projet"></span>
                    </h2>
                    <p class="text-gray-300">
                        Équipe : <span class="font-semibold" x-text="voteProjet?.nom_equipe"></span>
                    </p>

                    <div
                        x-cloak
                        x-show="voteStep === 1 || voteStep === 2"
                        class="absolute bottom-0 left-6 translate-y-1/2 bg-gray-800 px-3 py-1 rounded-full text-xs text-yellow-300 border border-gray-700"
                    >
                        <span x-text="`Étape ${voteStep} sur 2`"></span>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Étape 1 --}}
                    <div x-cloak x-show="voteStep === 1">
                        <div class="space-y-4">
                            <div>
                                <label for="nom_votant" class="block mb-2 text-sm font-medium text-gray-300">Votre nom (Optionnel)</label>
                                <input
                                    type="text"
                                    id="nom_votant"
                                    class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                    placeholder="Ex: Paul David Mbaye"
                                    x-model="nomVotant"
                                >
                            </div>

                            <div>
                                <label for="telephone_display" class="block mb-2 text-sm font-medium text-gray-300">Votre numéro de téléphone</label>
                                <div class="flex flex-row gap-0">
                                    <div class="flex-shrink-0 z-10 inline-flex items-center py-2.5 px-3 text-sm font-medium text-center text-gray-200 bg-gray-800 border border-gray-600 rounded-l-lg">
                                        🇸🇳 +221
                                    </div>
                                    <div class="relative w-full">
                                        <input
                                            type="tel"
                                            id="telephone_display"
                                            class="block p-2.5 w-full z-20 text-sm text-white bg-gray-700/50 rounded-r-lg border border-gray-600 border-l-0 focus:ring-2 focus:outline-none focus:ring-yellow-400"
                                            placeholder="Ex: 77 123 45 67"
                                            required
                                            x-model="telephoneDisplay"
                                        >
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-400">
                                    Vous recevrez un code unique par SMS pour valider votre vote.
                                </p>
                            </div>

                            <div class="pt-4 flex justify-center">
                                <div class="rainbow relative z-0 overflow-hidden p-0.5 flex items-center justify-center rounded-full hover:scale-105 transition duration-300 active:scale-100 w-full">
                                    <button
                                        type="button"
                                        class="w-full px-8 text-sm py-3 text-white rounded-full font-medium bg-gray-900 flex items-center justify-center"
                                        :disabled="isLoading"
                                        @click="sendOtp()"
                                    >
                                        <span x-show="!isLoading">Cliquez pour recevoir le code de vote</span>
                                        <span x-show="isLoading">Envoi en cours...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Étape 2 --}}
                    <div x-cloak x-show="voteStep === 2">
                        <div class="space-y-4">
                            <p class="text-center text-gray-300">Un code a été envoyé. Veuillez le saisir ci-dessous.</p>

                            <div>
                                <label for="code_otp" class="block mb-2 text-sm font-medium text-gray-300">Code de vérification (OTP)</label>
                                <input
                                    type="tel"
                                    id="code_otp"
                                    class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-2 px-3 text-white text-center text-2xl tracking-[1em]"
                                    placeholder="------"
                                    required
                                    maxlength="6"
                                    pattern="\d{6}"
                                    inputmode="numeric"
                                    x-model="otpCode"
                                >
                            </div>

                            <div class="pt-4">
                                <button
                                    type="button"
                                    class="w-full skew-btn bg-emerald-600 text-white hover:text-white flex items-center justify-center"
                                    :disabled="isLoading"
                                    @click="verifyOtp()"
                                >
                                    <span x-show="!isLoading">Valider le vote</span>
                                    <span x-show="isLoading">Validation...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Étape 3 --}}
                    <div x-cloak x-show="voteStep === 3" class="text-center py-6 px-4">
                        <div x-cloak x-show="successMessage" class="space-y-5">
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                                <div class="w-16 h-16 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-8 h-8 sm:w-7 sm:h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="text-center sm:text-left">
                                    <h3 class="text-xl sm:text-2xl font-semibold text-white mb-1">Vote enregistré</h3>
                                    <p class="text-sm text-gray-400" x-text="successMessage"></p>
                                </div>
                            </div>

                            <div class="pt-4 pb-2 border-t border-gray-800">
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 max-w-lg mx-auto">
                                    <div class="text-center sm:text-left flex-1">
                                        <h4 class="text-base font-medium text-white mb-1">Grande Finale</h4>
                                        <p class="text-xs text-gray-400">Réservez votre place dès maintenant</p>
                                    </div>
                                    <a href="https://reservation.govathon.sn" target="_blank"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-black font-medium text-sm rounded-lg transition-all duration-200 shadow-lg hover:shadow-yellow-500/20 flex-shrink-0">
                                        <span>Réserver</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <button @click="showVoteModal = false" class="text-sm text-gray-500 hover:text-gray-300 underline transition-colors">
                                Fermer
                            </button>
                        </div>

                        <div x-cloak x-show="errorMessage" class="space-y-4">
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

                    <!-- Erreur inline étape 1/2 -->
                    <div
                        x-cloak
                        x-show="errorMessage && (voteStep === 1 || voteStep === 2)"
                        class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg relative mt-4"
                        role="alert"
                    >
                        <p x-text="errorMessage"></p>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

<x-footer />

<!-- reCAPTCHA v3 -->
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

<script>
/**
 * Alpine component
 */
function voteJourJComponent(cfg) {
    return {
        // UI
        showModal: false,
        showWelcomeModal: false,
        modalProjet: null,

        showVoteModal: false,
        voteProjet: null,
        voteStep: 1,
        isLoading: false,
        errorMessage: '',
        successMessage: '',
        descriptionExpanded: false,

        // Vote status
        isVoteActive: !!cfg.isVoteActive,
        inactiveMessage: cfg.inactiveMessage || "Le vote Jour J n'est pas ouvert pour le moment.",

        // category
        selectedCategory: cfg.selectedCategory || 'all',
        categoryMenuOpen: false,

        // Event
        hasEvent: !!cfg.hasEvent,
        eventLat: cfg.event?.lat ?? null,
        eventLon: cfg.event?.lon ?? null,
        eventRadius: cfg.event?.radius ?? null,

        // GPS
        gpsStatus: 'idle', // idle | loading | success | error | denied | unavailable
        gpsMessage: 'Veuillez activer la localisation pour voter.',
        userLatitude: null,
        userLongitude: null,
        distanceToEvent: null,
        isInRange: false,

        // quick notice
        inactiveNoticeVisible: false,

        // OTP form state
        nomVotant: '',
        telephoneDisplay: '',
        otpCode: '',

        // urls / keys
        recaptchaKey: cfg.recaptchaKey || '',
        sendOtpUrl: cfg.sendOtpUrl,
        verifyOtpUrl: cfg.verifyOtpUrl,

        isSecureContextOk() {
            const isLocalhost = ['localhost', '127.0.0.1'].includes(window.location.hostname);
            return (window.location.protocol === 'https:' || isLocalhost) && window.isSecureContext;
        },

        init() {
            const self = this;

            // Toujours afficher la modale au chargement (pour forcer un clic utilisateur)
            self.showWelcomeModal = true;

            // Event actif ?
            if (!self.hasEvent) {
                self.gpsStatus = 'unavailable';
                self.gpsMessage = 'Vote Jour J désactivé';
            } else {
                self.tryAutoGPS();
            }

            // Vote désactivé globalement
            if (!self.isVoteActive) {
                self.voteStep = 3;
                self.errorMessage = self.inactiveMessage;
            }

            // Écouteurs global (optionnels)
            window.addEventListener('project-data', function(e) {
                self.modalProjet = e.detail;
                self.showModal = true;
                self.descriptionExpanded = false;
            });

            window.addEventListener('project-for-vote', function(e) {
                self.openVote(e.detail);
            });
        },

        closeAll() {
            this.showModal = false;
            this.showVoteModal = false;
            this.categoryMenuOpen = false;
        },

        showInactiveNotice() {
            this.inactiveNoticeVisible = true;
            setTimeout(() => { this.inactiveNoticeVisible = false }, 1200);
        },

        startFromWelcome() {
            this.showWelcomeModal = false;
            if (this.hasEvent) this.captureGPS(true);
        },

        async tryAutoGPS() {
            if (!navigator.geolocation) {
                this.gpsStatus = 'unavailable';
                this.gpsMessage = 'Votre navigateur ne supporte pas la géolocalisation.';
                return;
            }

            if (!this.isSecureContextOk()) {
                this.gpsStatus = 'unavailable';
                this.gpsMessage = 'Géolocalisation indisponible (HTTPS requis).';
                return;
            }

            if (navigator.permissions && navigator.permissions.query) {
                try {
                    const p = await navigator.permissions.query({ name: 'geolocation' });
                    if (p.state === 'granted') {
                        this.captureGPS(false);
                        return;
                    }
                    if (p.state === 'denied') {
                        this.gpsStatus = 'denied';
                        this.gpsMessage = '✗ Permission GPS refusée. Veuillez autoriser la géolocalisation.';
                        return;
                    }
                    this.gpsStatus = 'idle';
                    this.gpsMessage = 'Veuillez activer la localisation pour voter.';
                    return;
                } catch (e) {
                    this.gpsStatus = 'idle';
                    this.gpsMessage = 'Veuillez activer la localisation pour voter.';
                    return;
                }
            }

            this.gpsStatus = 'idle';
            this.gpsMessage = 'Veuillez activer la localisation pour voter.';
        },

        calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        },

        captureGPS(forceHighAccuracy = false) {
            if (!navigator.geolocation) {
                this.gpsStatus = 'unavailable';
                this.gpsMessage = 'Votre navigateur ne supporte pas la géolocalisation.';
                return;
            }

            if (!this.isSecureContextOk()) {
                this.gpsStatus = 'unavailable';
                this.gpsMessage = 'Géolocalisation indisponible (HTTPS requis).';
                return;
            }

            this.gpsStatus = 'loading';
            this.gpsMessage = 'Recherche de votre position...';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.userLatitude = position.coords.latitude;
                    this.userLongitude = position.coords.longitude;

                    if (this.hasEvent && this.eventLat != null && this.eventLon != null && this.eventRadius != null) {
                        this.distanceToEvent = this.calculateDistance(
                            this.userLatitude,
                            this.userLongitude,
                            this.eventLat,
                            this.eventLon
                        );

                        this.isInRange = this.distanceToEvent <= this.eventRadius;

                        if (this.isInRange) {
                            this.gpsStatus = 'success';
                            this.gpsMessage = `✓ Vous êtes dans la zone (${Math.round(this.distanceToEvent)}m)`;
                        } else {
                            this.gpsStatus = 'error';
                            this.gpsMessage = `❌ Vous n’êtes pas dans une zone autorisée (${Math.round(this.distanceToEvent)}m / ${this.eventRadius}m max)`;
                        }
                    } else {
                        this.gpsStatus = 'success';
                        this.gpsMessage = '✓ Position détectée';
                        this.isInRange = true;
                    }
                },
                (error) => {
                    console.error('Erreur GPS:', error);

                    if (error.code === error.PERMISSION_DENIED) {
                        this.gpsStatus = 'denied';
                        this.gpsMessage = '✗ Permission GPS refusée. Veuillez autoriser la géolocalisation.';
                        return;
                    }
                    if (error.code === error.POSITION_UNAVAILABLE) {
                        this.gpsStatus = 'error';
                        this.gpsMessage = '✗ Position GPS indisponible.';
                        return;
                    }
                    if (error.code === error.TIMEOUT) {
                        this.gpsStatus = 'error';
                        this.gpsMessage = '✗ Délai de géolocalisation dépassé.';
                        return;
                    }

                    this.gpsStatus = 'error';
                    this.gpsMessage = '✗ Erreur de géolocalisation.';
                },
                {
                    enableHighAccuracy: !!forceHighAccuracy,
                    timeout: 12000,
                    maximumAge: 0
                }
            );
        },

        openDetails(projet) {
            this.modalProjet = projet;
            this.showModal = true;
            this.descriptionExpanded = false;
        },

        voteButtonDisabled() {
            return (!this.hasEvent || !this.isVoteActive || !this.isInRange || this.gpsStatus !== 'success');
        },

        voteButtonClass() {
            return {
                'bg-green-400/75 text-gray-100 hover:bg-yellow-300 hover:text-black':
                    this.hasEvent && this.isVoteActive && this.isInRange && this.gpsStatus === 'success',
                'bg-gray-600 text-gray-300 cursor-not-allowed':
                    this.voteButtonDisabled()
            };
        },

        explainWhyVoteDisabled() {
            if (!this.hasEvent) {
                alert('❌ Le système de vote Jour J est désactivé. Aucun événement actif.');
                return;
            }
            if (!this.isVoteActive) {
                this.showInactiveNotice();
                return;
            }
            if (!this.isSecureContextOk()) {
                alert('⚠️ La géolocalisation nécessite HTTPS (ou localhost).');
                return;
            }
            if (this.gpsStatus !== 'success') {
                alert('⚠️ Activez la localisation puis attendez la détection GPS.');
                return;
            }
            if (!this.isInRange) {
                alert('❌ Vous devez être sur place pour voter' + (this.distanceToEvent ? ' (distance: ' + Math.round(this.distanceToEvent) + 'm)' : ''));
                return;
            }
        },

        openVote(projet) {
            if (this.voteButtonDisabled()) {
                this.explainWhyVoteDisabled();
                return;
            }

            this.voteProjet = projet;
            this.showVoteModal = true;

            this.voteStep = this.isVoteActive ? 1 : 3;
            this.errorMessage = this.isVoteActive ? '' : this.inactiveMessage;
            this.successMessage = '';
            this.otpCode = '';
        },

        normalizeSNPhone(raw) {
            const digits = String(raw || '').replace(/\D+/g, '');
            const last9 = digits.length >= 9 ? digits.slice(-9) : digits;
            if (last9.length !== 9) return null;
            return '+221' + last9;
        },

        csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },

        async sendOtp() {
            this.errorMessage = '';
            this.successMessage = '';

            if (this.voteButtonDisabled()) {
                this.explainWhyVoteDisabled();
                return;
            }

            const phone = this.normalizeSNPhone(this.telephoneDisplay);
            if (!phone) {
                this.errorMessage = 'Numéro invalide. Exemple: 77 123 45 67';
                return;
            }

            if (!this.voteProjet?.id) {
                this.errorMessage = 'Projet invalide.';
                return;
            }

            // reCAPTCHA v3 (si clé dispo)
            let recaptchaToken = '';
            try {
                if (this.recaptchaKey && window.grecaptcha && window.grecaptcha.execute) {
                    recaptchaToken = await window.grecaptcha.execute(this.recaptchaKey, { action: 'vote_jourj' });
                }
            } catch (e) {}

            this.isLoading = true;

            try {
                const res = await fetch(this.sendOtpUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        projet_id: this.voteProjet.id,
                        telephone: phone,
                        nom_votant: this.nomVotant || null,
                        recaptcha_token: recaptchaToken || null
                    })
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok || !data.success) {
                    this.errorMessage = data.message || 'Erreur lors de l’envoi du code.';
                    return;
                }

                this.voteStep = 2;
                this.errorMessage = '';
            } catch (err) {
                console.error(err);
                this.errorMessage = 'Erreur réseau. Veuillez réessayer.';
            } finally {
                this.isLoading = false;
            }
        },

        async verifyOtp() {
            this.errorMessage = '';
            this.successMessage = '';

            const code = String(this.otpCode || '').replace(/\D+/g, '');
            if (code.length !== 6) {
                this.errorMessage = 'Code OTP invalide (6 chiffres).';
                return;
            }

            if (this.userLatitude == null || this.userLongitude == null) {
                this.errorMessage = 'Position GPS non disponible. Activez la localisation puis réessayez.';
                return;
            }

            this.isLoading = true;

            try {
                const res = await fetch(this.verifyOtpUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code_otp: code,
                        latitude: this.userLatitude,
                        longitude: this.userLongitude
                    })
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok || !data.success) {
                    this.errorMessage = data.message || 'Validation OTP échouée.';
                    return;
                }

                this.voteStep = 3;
                this.successMessage = data.message || 'Vote enregistré avec succès !';
                this.errorMessage = '';
            } catch (err) {
                console.error(err);
                this.errorMessage = 'Erreur réseau. Veuillez réessayer.';
            } finally {
                this.isLoading = false;
            }
        },
    };
}
</script>

<!-- Partage projet -->
<script>
window.shareProjectForProject = function (id, projectName) {
    const urlObj = new URL(window.location.href);

    urlObj.searchParams.delete('vote');
    urlObj.searchParams.delete('project_id');
    urlObj.searchParams.delete('page');

    if (projectName && typeof projectName === 'string') {
        urlObj.searchParams.set('search', projectName);
    }

    const finalUrl = urlObj.toString();

    const title = document.title || 'GovAthon – Découvrir un projet';
    const text  = 'Découvrez ce projet et votez pour lui :';

    if (typeof window.shareProject === 'function') {
        window.shareProject(finalUrl);
        return;
    }

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
};
</script>

<!-- Deep link auto-vote -->
<script>
(function () {
    let voteDeepLinkHandled = false;

    function openVoteFromUrl() {
        if (voteDeepLinkHandled) return;
        voteDeepLinkHandled = true;

        const params = new URLSearchParams(window.location.search);
        const wantVote  = params.get('vote') === '1';
        const projectId = params.get('project_id');

        if (!wantVote || !projectId) return;

        fetch('/vote/project/' + projectId + '/data')
            .then(function (res) {
                if (!res.ok) throw new Error('Impossible de charger le projet');
                return res.json();
            })
            .then(function (data) {
                window.dispatchEvent(new CustomEvent('project-for-vote', { detail: data }));

                if (history && history.replaceState) {
                    const cleanUrl = new URL(window.location);
                    cleanUrl.searchParams.delete('vote');
                    cleanUrl.searchParams.delete('project_id');
                    history.replaceState({}, '', cleanUrl.toString());
                }
            })
            .catch(function (err) {
                console.error('❌ Erreur auto-vote / fetch projet :', err);
            });
    }

    document.addEventListener('alpine:initialized', function () {
        openVoteFromUrl();
    });

    window.addEventListener('load', function () {
        setTimeout(openVoteFromUrl, 400);
    });
})();
</script>

<!-- Recherche auto -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-input');
    if (!input) return;

    const form = input.closest('form');
    if (!form) return;

    let timer = null;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 400);
    });
});
</script>

</body>
</html>
