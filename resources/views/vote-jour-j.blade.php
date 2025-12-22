{{-- resources/views/vote-jour-j.blade.php --}}
<!DOCTYPE html>
<html lang="fr" class="overflow-x-hidden">
<head>
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-image-custom { background-image: url('{{ asset('assets/img/bg-vote.jpg') }}'); }

        /* Modales */
        .modal-overlay{ position:fixed; inset:0; background:rgba(0,0,0,.6); backdrop-filter: blur(6px); display:none; align-items:center; justify-content:center; z-index:9999; padding:16px; }
        .modal-overlay.open{ display:flex; }
        .modal-box{ width:100%; max-width:720px; background:rgba(17,24,39,.95); border:1px solid rgba(234,179,8,.25); border-radius:16px; box-shadow: 0 20px 60px rgba(0,0,0,.45); overflow:hidden; }
        .modal-head{ padding:18px 20px; border-bottom:1px solid rgba(55,65,81,.8); position:relative; }
        .modal-body{ padding:18px 20px; max-height:72vh; overflow:auto; }
        .btn-close{ position:absolute; right:10px; top:10px; width:38px; height:38px; border-radius:999px; display:flex; align-items:center; justify-content:center; color:#9ca3af; }
        .btn-close:hover{ background:rgba(55,65,81,.8); color:white; }

        .badge-step{ position:absolute; left:20px; bottom:-12px; background:rgba(31,41,55,.95); border:1px solid rgba(55,65,81,.9); color:#facc15; font-size:12px; padding:4px 10px; border-radius:999px; }

        /* mini toast */
        .toast{ position:fixed; top:14px; left:50%; transform:translateX(-50%); background:rgba(127,29,29,.95); border:1px solid rgba(239,68,68,.5); color:#fee2e2; font-size:13px; padding:10px 14px; border-radius:999px; z-index:10000; display:none; }
        .toast.show{ display:block; }

        /* GPS panel inside modal */
        .gps-card{
            border:1px solid rgba(55,65,81,.8);
            background: rgba(2,6,23,.35);
            border-radius: 14px;
            padding: 14px;
        }
        .gps-pill{
            font-size:12px;
            padding:4px 10px;
            border-radius:999px;
            border:1px solid rgba(55,65,81,.8);
            background: rgba(17,24,39,.6);
            color:#e5e7eb;
            display:inline-flex;
            align-items:center;
            gap:8px;
        }
        .gps-dot{ width:8px;height:8px;border-radius:999px; background:#9ca3af; }
        .gps-dot.ok{ background:#34d399; }
        .gps-dot.bad{ background:#fb7185; }
        .gps-dot.warn{ background:#facc15; }
        .mono{ font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    </style>
</head>

<body class="bg-black text-white flex flex-col min-h-screen bg-cover bg-center bg-fixed bg-image-custom overflow-x-hidden">

<x-header />

@php
    $isVoteActive = $isVoteActive ?? true;
    $inactiveMessage = $inactiveMessage ?? "Le vote Jour J n'est pas ouvert pour le moment.";

    $hasEvent = (bool) $event;
    $eventPayload = $event ? [
        'id' => (int) $event->id,
        'name' => (string) ($event->nom ?? 'Événement Jour J'),
        'lat' => (float) $event->latitude,
        'lon' => (float) $event->longitude,
        'radius' => (int) $event->rayon_metres,
    ] : null;

    $__projectsCount = 0;
    foreach ($secteurs as $__s) { $__projectsCount += $__s->projets->count(); }
@endphp

<div id="toast" class="toast"></div>

<main class="flex-grow container mx-auto px-4 py-12 overflow-x-hidden">
    <div class="bg-black bg-opacity-60 p-8 rounded-lg shadow-2xl max-w-6xl mx-auto w-full">

        <div class="mb-4 flex items-center justify-between gap-3 flex-wrap">
            <a href="{{ route('vote.index') }}"
               class="inline-flex items-center gap-2 text-gray-400 hover:text-yellow-400 transition-colors text-sm">
                <span>←</span> Retour
            </a>

            <div class="text-xs text-gray-300">
                <!-- Zone active information hidden -->
            </div>
        </div>

        <div class="text-center mb-4 px-2">
            <h1 class="text-3xl md:text-4xl font-bold text-yellow-400 mb-2">🏆 GRANDE FINALE JOUR J</h1>

            @if(!$event)
                <div class="mt-2 px-4 py-2 bg-red-900/30 border border-red-700 rounded-lg inline-block">
                    <p class="text-red-300 text-xs">❌ Le vote Jour J est actuellement désactivé</p>
                </div>
            @endif

            <p class="mt-3 text-gray-300 text-sm sm:text-base">
                Recherchez un projet, une équipe, puis votez.
                <span class="text-gray-500">({{ $__projectsCount }} projet(s))</span>
            </p>
        </div>

        {{-- Recherche --}}
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
                    </div>

                    <button type="submit"
                            class="px-4 py-3 rounded-lg font-semibold bg-yellow-500 hover:bg-yellow-600 text-black">
                        Rechercher
                    </button>
                </div>
            </form>
        </div>

        {{-- Tableau --}}
        <div class="overflow-x-auto">
            <table class="vj-table w-full text-left border-collapse md:max-w-5xl md:mx-auto">
                <thead class="bg-gray-800">
                <tr>
                    <th class="p-4 text-base font-semibold text-gray-100">Secteur</th>
                    <th class="p-4 text-base font-semibold text-gray-100">Équipe</th>
                    <th class="p-4 text-base font-semibold text-gray-100">Projet</th>
                    <th class="p-4 text-base font-semibold text-gray-100 text-center">Actions</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($secteurs as $secteur)
                    @forelse ($secteur->projets as $projet)
                        @php
                            $projetJs = [
                                'id' => $projet->id,
                                'secteur' => $secteur->nom,
                                'nom_projet' => $projet->nom_projet,
                                'nom_equipe' => $projet->nom_equipe,
                                'resume' => $projet->resume,
                                'description' => $projet->description,
                            ];

                            $demoUrl = $projet->video_demo
                                ?? $projet->video_demonstration
                                ?? \Illuminate\Support\Facades\DB::table('liste_preselectionnes')->where('projet_id', $projet->id)->value('video_demo')
                                ?? \Illuminate\Support\Facades\DB::table('liste_preselectionnes')->where('projet_id', $projet->id)->value('video_demonstration');
                        @endphp

                        <tr class="border-b border-gray-700 bg-gray-800/40 vj-card-row">
                            <td class="p-4 text-sm text-gray-200 vj-sector">{{ $secteur->nom }}</td>
                            <td class="p-4 text-sm text-gray-200 vj-team">{{ $projet->nom_equipe }}</td>
                            <td class="p-4 text-sm font-semibold text-white vj-project">{{ $projet->nom_projet }}</td>
                            <td class="p-4">
                                <div class="vj-actions">
                                    <!-- Mobile layout: icon row + full-width vote button -->
                                    <div class="vj-mobile-actions">
                                        <div class="vj-icon-row">
                                            <button type="button"
                                                    class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                    aria-label="Détails du projet"
                                                    title="Détails"
                                                    data-action="details"
                                                    data-projet='@json($projetJs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <!-- Share button -->
                                            <button type="button"
                                                    class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                    title="Partager ce projet"
                                                    onclick="shareProjectForProject({{ $projet->id }}, @js($projet->nom_projet))">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                                </svg>
                                            </button>

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

                                        <button type="button" class="vj-mobile-vote vj-vote-btn px-3 py-2 rounded-lg bg-green-500/80 hover:bg-yellow-400 hover:text-black text-white text-sm font-bold"
                                                data-action="vote" data-projet='@json($projetJs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
                                            Voter
                                        </button>
                                    </div>

                                    <!-- Desktop layout: icon-only buttons + inline vote -->
                                    <div class="vj-desktop-actions">
                                        <button type="button"
                                                class="vj-icon-btn p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                aria-label="Détails du projet"
                                                title="Détails"
                                                data-action="details"
                                                data-projet='@json($projetJs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <button type="button"
                                                class="p-2 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
                                                title="Partager ce projet"
                                                onclick="shareProjectForProject({{ $projet->id }}, @js($projet->nom_projet))">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                                            </svg>
                                        </button>

                                        <button type="button" class="vj-vote-btn px-3 py-2 rounded-lg bg-green-500/80 hover:bg-yellow-400 hover:text-black text-white text-sm font-bold"
                                                data-action="vote" data-projet='@json($projetJs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)'>
                                            Voter
                                        </button>

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
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
</main>

<x-footer />

{{-- ====================== MODALE DETAILS ====================== --}}
<div id="detailsModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box">
        <div class="modal-head">
            <button type="button" class="btn-close" data-close="detailsModal">✕</button>
            <h2 id="detailsTitle" class="text-xl sm:text-2xl font-bold text-yellow-400"></h2>
            <p class="text-gray-300 mt-1">Équipe : <span id="detailsTeam" class="font-semibold text-white"></span></p>
        </div>
        <div class="modal-body space-y-4 text-gray-200">
            <div>
                <div class="text-gray-300 font-semibold">Résumé</div>
                <div id="detailsResume" class="whitespace-pre-wrap text-sm mt-1"></div>
            </div>
            <div>
                <div class="text-gray-300 font-semibold">Description</div>
                <div id="detailsDesc" class="whitespace-pre-wrap text-sm mt-1"></div>
            </div>
        </div>
    </div>
</div>

{{-- ====================== MODALE VOTE ====================== --}}
<div id="voteModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:640px;">
        <div class="modal-head">
            <button type="button" class="btn-close" data-close="voteModal">✕</button>
            <h2 class="text-xl sm:text-2xl font-bold text-yellow-400">
                Voter pour : <span id="voteTitle" class="text-white"></span>
            </h2>
            <p class="text-gray-300 mt-1">Équipe : <span id="voteTeam" class="font-semibold text-white"></span></p>
            <div id="voteStepBadge" class="badge-step">Étape 1 sur 2</div>
        </div>

        <div class="modal-body space-y-4">

            {{-- GPS PANEL (minimal) --}}
            <div class="gps-card">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <span class="gps-pill">
                        <span id="gpsDot" class="gps-dot"></span>
                        <span id="gpsStatusText">Localisation : non vérifiée</span>
                    </span>

                    <button type="button" id="btnRefreshGPS"
                            class="text-xs px-3 py-1 rounded-full border border-gray-700 bg-gray-900/40 hover:bg-gray-900/70">
                        Vérifier ma position
                    </button>
                </div>
            </div>

            <div id="voteError" class="hidden bg-red-900/50 border border-red-700 text-red-200 px-4 py-3 rounded-lg"></div>
            <div id="voteSuccess" class="hidden bg-emerald-900/30 border border-emerald-600/40 text-emerald-100 px-4 py-3 rounded-lg"></div>

            {{-- Step 1 --}}
            <div id="step1" class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-300">Votre nom (optionnel)</label>
                    <input type="text" id="nom_votant"
                           class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                           placeholder="Ex: Paul David Mbaye">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-300">Votre numéro de téléphone</label>
                    <div class="flex flex-row gap-0">
                        <div class="flex-shrink-0 inline-flex items-center py-2.5 px-3 text-sm font-medium text-gray-200 bg-gray-800 border border-gray-600 rounded-l-lg">
                            🇸🇳 +221
                        </div>
                        <div class="relative w-full">
                            <input type="tel" id="telephone_display"
                                   class="block p-2.5 w-full text-sm text-white bg-gray-700/50 rounded-r-lg border border-gray-600 border-l-0 focus:ring-2 focus:outline-none focus:ring-yellow-400"
                                   placeholder="Ex: 77 123 45 67"
                                   inputmode="numeric"
                                   autocomplete="tel">
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-400">Vous recevrez un code par SMS.</p>
                </div>

                <div class="pt-4 flex justify-center">
                    <div class="rainbow relative z-0 overflow-hidden p-0.5 flex items-center justify-center rounded-full hover:scale-105 transition duration-300 active:scale-100 w-full">
                        <button type="button" id="btnSendOtp"
                                class="w-full px-8 text-sm py-3 text-white rounded-full font-medium bg-gray-900 flex items-center justify-center"
                                aria-pressed="false">
                            <span class="btn-label">Cliquez pour recevoir le code de vote</span>
                            <span class="btn-loading hidden">Envoi en cours...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Step 2 --}}
            <div id="step2" class="space-y-4 hidden">
                <p class="text-center text-gray-300">Saisissez le code reçu par SMS.</p>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-300">Code OTP (6 chiffres)</label>
                    <input type="tel" id="code_otp"
                           class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-2 px-3 text-white text-center text-2xl tracking-[1em]"
                           placeholder="------"
                           maxlength="6" inputmode="numeric"
                           autocomplete="one-time-code">
                </div>

                <button type="button" id="btnVerifyOtp"
                        class="w-full px-4 py-3 rounded-lg font-bold bg-emerald-600 hover:bg-emerald-700 text-white disabled:opacity-50 disabled:cursor-not-allowed">
                    Valider le vote
                </button>
            </div>
        </div>
    </div>
</div>

@if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endif

<script>
/* ====================== CONFIG SERVEUR ====================== */
const CFG = {
    isVoteActive: @json((bool)$isVoteActive),
    inactiveMessage: @json($inactiveMessage),
    hasEvent: @json((bool)$hasEvent),
    event: @json($eventPayload),
    sendOtpUrl: @json(route('vote-jour-j.envoyerOtp')),
    verifyOtpUrl: @json(route('vote-jour-j.verifierOtp')),
    recaptchaKey: @json(config('services.recaptcha.site_key')),
};

/* ====================== UTIL ====================== */
function $(id){ return document.getElementById(id); }
function show(el){ if(el) el.classList.remove('hidden'); }
function hide(el){ if(el) el.classList.add('hidden'); }
function openModal(id){ const el=$(id); if(el) el.classList.add('open'); }
function closeModal(id){ const el=$(id); if(el) el.classList.remove('open'); }
function toast(msg){
    const t = $('toast');
    if(!t) return;
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'), 1600);
}
function csrfToken(){
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}
function normalizeSNPhone(raw){
    const digits = String(raw||'').replace(/\D+/g,'');
    const last9 = digits.length >= 9 ? digits.slice(-9) : digits;
    if(last9.length !== 9) return null;
    return '+221' + last9;
}
function isSecureContextOk(){
    const isLocalhost = ['localhost','127.0.0.1'].includes(window.location.hostname);
    return (window.location.protocol === 'https:' || isLocalhost) && !!window.isSecureContext;
}

/* ====================== STATE VOTE ====================== */
let currentProject = null;
let step = 1;

/* ====================== GPS ====================== */
const REQUIRE_GPS = true;
let gpsWatchId = null;
let gpsRequestPromise = null;

let gps = {
    status:'idle',
    lat:null, lon:null,
    accuracy:null,
    inRange:false,
    dist:null,
    lastAt:null,
    bearing:null
};

// ✅ Freeze GPS approval (pour éviter re-blocage sur jitter)
let gpsApproved = false;
let gpsApprovedAt = null;
function approveGps(){ gpsApproved = true; gpsApprovedAt = Date.now(); }
function resetGpsApproval(){ gpsApproved = false; gpsApprovedAt = null; }

function calcDistance(lat1, lon1, lat2, lon2){
    const R = 6371000;
    const φ1 = lat1*Math.PI/180, φ2 = lat2*Math.PI/180;
    const Δφ = (lat2-lat1)*Math.PI/180, Δλ = (lon2-lon1)*Math.PI/180;
    const a = Math.sin(Δφ/2)**2 + Math.cos(φ1)*Math.cos(φ2)*Math.sin(Δλ/2)**2;
    return R * (2*Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
}
function clamp(n, min, max){ return Math.max(min, Math.min(max, n)); }
function computeToleranceMeters(accuracy){
    const acc = (accuracy != null && !isNaN(accuracy)) ? accuracy : 0;
    return clamp(Math.round(acc), 15, 60);
}
function computeInRange(dist, radius, accuracy){
    if(dist == null || radius == null) return true;
    const tol = computeToleranceMeters(accuracy);
    return dist <= (radius + tol);
}
function isGpsFresh(maxAgeMs = 15000){
    return gps.lastAt && (Date.now() - gps.lastAt) <= maxAgeMs;
}
function setGpsDot(state){
    const dot = $('gpsDot');
    if(!dot) return;
    dot.classList.remove('ok','bad','warn');
    if(state === 'ok') dot.classList.add('ok');
    else if(state === 'bad') dot.classList.add('bad');
    else if(state === 'warn') dot.classList.add('warn');
}

function updateGpsUI(){
    const st = $('gpsStatusText');
    if(!st) return;

    if(!REQUIRE_GPS){
        setGpsDot('ok');
        st.textContent = 'Localisation : non requise';
        return;
    }

    if(!CFG.hasEvent){
        setGpsDot('bad');
        st.textContent = 'Localisation : événement non actif';
        return;
    }

    if(gps.status === 'checking'){
        setGpsDot('warn');
        st.textContent = 'Localisation : vérification en cours…';
        return;
    }

    if(gps.status === 'error'){
        setGpsDot('bad');
        st.textContent = 'Localisation : impossible (autorisez la localisation)';
        return;
    }

    if(gps.status === 'success'){
        if(gpsApproved || gps.inRange){
            setGpsDot('ok');
            st.textContent = 'Localisation : ✅ zone autorisée';
        } else {
            setGpsDot('bad');
            st.textContent = 'Localisation : ❌ zone non autorisée';
        }
        return;
    }

    setGpsDot('');
    st.textContent = 'Localisation : non vérifiée';
}

// ✅ Step 2: on ne rebloque pas côté front
function lockButtonsByGPS(){
    const sendBtn = $('btnSendOtp');
    const verifyBtn = $('btnVerifyOtp');
    if(!sendBtn || !verifyBtn) return;

    if(step === 1){
        if(!REQUIRE_GPS){
            sendBtn.disabled = false;
            verifyBtn.disabled = true;
            return;
        }
        const ok = gpsApproved || (gps.status === 'success' && gps.inRange === true);
        sendBtn.disabled = !ok;
        verifyBtn.disabled = true;
        return;
    }

    // step 2
    sendBtn.disabled = true;
    verifyBtn.disabled = false;
}

async function captureGPS(){
    console.log('DEBUG: captureGPS called', { REQUIRE_GPS, hasEvent: CFG.hasEvent, protocol: window.location.protocol, isSecureContext: !!window.isSecureContext });
    if(!REQUIRE_GPS) {
        gps = {...gps, status:'success', inRange:true, lastAt: Date.now()};
        approveGps();
        updateGpsUI(); lockButtonsByGPS();
        return true;
    }
    if(!CFG.hasEvent){
        gps.status = 'error';
        updateGpsUI(); lockButtonsByGPS();
        toast('Vote Jour J désactivé (aucun événement).');
        return false;
    }
    if(!navigator.geolocation){
        gps.status = 'error';
        updateGpsUI(); lockButtonsByGPS();
        toast('GPS non supporté sur ce navigateur.');
        return false;
    }
    if(!isSecureContextOk()){
        gps.status = 'error';
        updateGpsUI(); lockButtonsByGPS();
        toast('GPS nécessite HTTPS (ou localhost).');
        return false;
    }

    // ✅ si déjà approuvé, on ne refait pas de blocage
    if(gpsApproved){
        gps.status = gps.status === 'idle' ? 'success' : gps.status;
        updateGpsUI(); lockButtonsByGPS();
        return true;
    }

    // ✅ si déjà OK et frais
    if(gps.status === 'success' && gps.inRange === true && isGpsFresh(20000)){
        approveGps();
        lockButtonsByGPS();
        return true;
    }

    if(gpsRequestPromise) return gpsRequestPromise;

    gps.status = 'checking';
    updateGpsUI(); lockButtonsByGPS();

    const getPos = (opts) => new Promise((resolve, reject)=>{
        navigator.geolocation.getCurrentPosition(resolve, reject, opts);
    });

    const applyPos = (pos) => {
        gps.lat = pos.coords.latitude;
        gps.lon = pos.coords.longitude;
        gps.accuracy = pos.coords.accuracy ?? null;
        gps.lastAt = Date.now();

        gps.dist = calcDistance(gps.lat, gps.lon, CFG.event.lat, CFG.event.lon);
        gps.inRange = computeInRange(gps.dist, CFG.event.radius, gps.accuracy);
        gps.status = 'success';

        if(gps.inRange === true){
            approveGps(); // ✅ on gèle l'autorisation
        }

        updateGpsUI(); lockButtonsByGPS();
        return gps.inRange;
    };

    gpsRequestPromise = (async ()=>{
        try{
            const posFast = await getPos({ enableHighAccuracy:false, timeout:4000, maximumAge:30000 });
            const okFast = applyPos(posFast);

            if(okFast){
                // amélioration silencieuse
                getPos({ enableHighAccuracy:true, timeout:12000, maximumAge:0 }).then(applyPos).catch(()=>{});
                return true;
            }

            const posHi = await getPos({ enableHighAccuracy:true, timeout:12000, maximumAge:0 });
            return applyPos(posHi);
        } catch(err){
            console.error(err);
            gps.status = 'error';
            updateGpsUI(); lockButtonsByGPS();
            toast('Impossible d’obtenir la position. Autorisez la localisation.');
            return false;
        } finally {
            gpsRequestPromise = null;
        }
    })();

    return gpsRequestPromise;
}

function startWatchGPS(){
    if(!REQUIRE_GPS || !CFG.hasEvent) return;
    if(!navigator.geolocation) return;
    if(!isSecureContextOk()) return;

    stopWatchGPS();

    gpsWatchId = navigator.geolocation.watchPosition((pos)=>{
        // ✅ si déjà approuvé, on n'essaie plus de recalculer inRange (évite jitter)
        if(gpsApproved){
            gps.lat = pos.coords.latitude;
            gps.lon = pos.coords.longitude;
            gps.accuracy = pos.coords.accuracy ?? null;
            gps.lastAt = Date.now();
            return;
        }

        gps.lat = pos.coords.latitude;
        gps.lon = pos.coords.longitude;
        gps.accuracy = pos.coords.accuracy ?? null;
        gps.lastAt = Date.now();

        gps.dist = calcDistance(gps.lat, gps.lon, CFG.event.lat, CFG.event.lon);
        gps.inRange = computeInRange(gps.dist, CFG.event.radius, gps.accuracy);
        gps.status = 'success';

        if(gps.inRange === true){
            approveGps();
        }

        updateGpsUI();
        lockButtonsByGPS();
    }, (err)=>{
        console.warn(err);
    }, { enableHighAccuracy:true, maximumAge:2000, timeout:12000 });
}

function stopWatchGPS(){
    if(gpsWatchId != null && navigator.geolocation){
        navigator.geolocation.clearWatch(gpsWatchId);
    }
    gpsWatchId = null;
}

/* ====================== VOTE UI ====================== */
function resetVoteUI(){
    step = 1;
    resetGpsApproval();
    gps = { status:'idle', lat:null, lon:null, accuracy:null, inRange:false, dist:null, lastAt:null, bearing:null };

    if($('voteStepBadge')) $('voteStepBadge').textContent = 'Étape 1 sur 2';
    if($('nom_votant')) $('nom_votant').value = '';
    if($('telephone_display')) $('telephone_display').value = '';
    if($('code_otp')) $('code_otp').value = '';

    hide($('voteError'));
    hide($('voteSuccess'));
    show($('step1'));
    hide($('step2'));

    if($('btnSendOtp')) $('btnSendOtp').disabled = true;
    if($('btnVerifyOtp')) $('btnVerifyOtp').disabled = true;

    updateGpsUI();
    lockButtonsByGPS();
}

function setVoteError(msg){
    const el = $('voteError');
    if(!el) return;
    el.textContent = msg || 'Erreur';
    show(el);
}
function setVoteSuccess(msg){
    const el = $('voteSuccess');
    if(!el) return;
    el.textContent = msg || 'Succès';
    show(el);
}
function goStep2(){
    step = 2;
    if($('voteStepBadge')) $('voteStepBadge').textContent = 'Étape 2 sur 2';
    hide($('step1'));
    show($('step2'));
    lockButtonsByGPS();
}

/* ====================== MODALES / CLICK ====================== */
document.addEventListener('click', async (e)=>{
    const btn = e.target.closest('[data-action]');
    if(!btn) return;

    const action = btn.getAttribute('data-action');
    const raw = btn.getAttribute('data-projet');
    let projet = null;

    try{ projet = JSON.parse(raw); } catch(err){ console.error(err); alert('Projet invalide'); return; }

    if(action === 'details'){
        if($('detailsTitle')) $('detailsTitle').textContent = projet.nom_projet || '';
        if($('detailsTeam')) $('detailsTeam').textContent = projet.nom_equipe || '';
        if($('detailsResume')) $('detailsResume').textContent = projet.resume || '';
        if($('detailsDesc')) $('detailsDesc').textContent = projet.description || '';
        openModal('detailsModal');
        return;
    }

    if(action === 'vote'){
        if(!CFG.hasEvent){ alert('❌ Vote Jour J désactivé (aucun événement actif).'); return; }
        if(!CFG.isVoteActive){ toast(CFG.inactiveMessage || 'Vote fermé'); return; }

        currentProject = projet;
        if($('voteTitle')) $('voteTitle').textContent = projet.nom_projet || '';
        if($('voteTeam')) $('voteTeam').textContent = projet.nom_equipe || '';

        resetVoteUI();
        openModal('voteModal');

        console.log('DEBUG: vote action -> opening modal and requesting GPS for project', projet.id);
        const ok = await captureGPS();
        startWatchGPS();

        if(!ok){
            setVoteError("Vous n'êtes pas dans la zone de vote autorisée.");
        }
        return;
    }
});

// fermer modales
document.addEventListener('click', (e)=>{
    const closeBtn = e.target.closest('[data-close]');
    if(closeBtn){
        const id = closeBtn.getAttribute('data-close');
        closeModal(id);
        if(id === 'voteModal') stopWatchGPS();
    }
});
document.addEventListener('keydown', (e)=>{
    if(e.key === 'Escape'){
        closeModal('detailsModal');
        closeModal('voteModal');
        stopWatchGPS();
    }
});
['detailsModal','voteModal'].forEach(id=>{
    const el = $(id);
    if(!el) return;
    el.addEventListener('click', (e)=>{
        if(e.target === el){
            closeModal(id);
            if(id === 'voteModal') stopWatchGPS();
        }
    });
});

/* ====================== GPS refresh ====================== */
const btnRefresh = $('btnRefreshGPS');
if(btnRefresh){
    btnRefresh.addEventListener('click', async ()=>{
        hide($('voteError'));
        resetGpsApproval(); // l'utilisateur force une revérif
        const ok = await captureGPS();
        if(!ok){
            setVoteError("Vous n'êtes pas dans la zone de vote autorisée.");
        }else{
            toast('✅ Zone autorisée');
        }
    });
}

/* ====================== OTP ====================== */
async function getRecaptchaToken(){
    try{
        if(CFG.recaptchaKey && window.grecaptcha && window.grecaptcha.execute){
            return await window.grecaptcha.execute(CFG.recaptchaKey, { action: 'vote_jourj' });
        }
    }catch(e){}
    return '';
}
// Helper to toggle send-OTP button loading state (shows/hides spans)
function setSendOtpLoading(state){
    const btn = $('btnSendOtp');
    if(!btn) return;
    const label = btn.querySelector('.btn-label');
    const loading = btn.querySelector('.btn-loading');
    if(state){
        btn.setAttribute('aria-busy','true');
        btn.disabled = true;
        if(label) label.classList.add('hidden');
        if(loading) loading.classList.remove('hidden');
    } else {
        btn.removeAttribute('aria-busy');
        if(label) label.classList.remove('hidden');
        if(loading) loading.classList.add('hidden');
        // restore enabled state according to GPS rules
        lockButtonsByGPS();
    }
}

const btnSendOtp = $('btnSendOtp');
if(btnSendOtp){
    btnSendOtp.addEventListener('click', async ()=>{
        hide($('voteError'));
        hide($('voteSuccess'));

        setSendOtpLoading(true);

        try{
            if(!currentProject?.id){ setVoteError('Projet invalide.'); return; }

            // ✅ step1: on exige la zone, mais si approuvé => pas de re-check bloquant
            if(REQUIRE_GPS && !gpsApproved){
                const ok = await captureGPS();
                if(!ok){
                    setVoteError("Vous n'êtes pas dans la zone de vote autorisée.");
                    return;
                }
            }

            const phone = normalizeSNPhone($('telephone_display')?.value);
            if(!phone){ setVoteError('Numéro invalide. Exemple: 77 123 45 67'); return; }

            // keep button disabled and show loading
            btnSendOtp.disabled = true;

            const recaptchaToken = await getRecaptchaToken();

            const res = await fetch(CFG.sendOtpUrl, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept':'application/json'
                },
                body: JSON.stringify({
                    projet_id: currentProject.id,
                    telephone: phone,
                    nom_votant: $('nom_votant')?.value || null,
                    recaptcha_token: recaptchaToken || null
                })
            });

            const data = await res.json().catch(()=>({}));
            if(!res.ok || !data.success){
                setVoteError(data.message || "Erreur lors de l’envoi du code.");
                lockButtonsByGPS();
                return;
            }

            goStep2();
            stopWatchGPS(); // ✅ plus besoin du watch en step2
            toast('📩 Code envoyé');

        }catch(err){
            console.error(err);
            setVoteError('Erreur réseau. Réessayez.');
            lockButtonsByGPS();
        } finally {
            setSendOtpLoading(false);
        }
    });
}

const btnVerifyOtp = $('btnVerifyOtp');
if(btnVerifyOtp){
    btnVerifyOtp.addEventListener('click', async ()=>{
        hide($('voteError'));
        hide($('voteSuccess'));

        const code = String($('code_otp')?.value||'').replace(/\D+/g,'');
        if(code.length !== 6){ setVoteError('Code OTP invalide (6 chiffres).'); return; }

        // ✅ step2: on ne refait PAS de validation "inRange" côté front (évite jitter)
        // On s'assure juste d'avoir des coordonnées
        if(gps.lat == null || gps.lon == null){
            try{
                if(!navigator.geolocation){
                    setVoteError("GPS non supporté sur ce navigateur.");
                    return;
                }
                if(!isSecureContextOk()){
                    setVoteError("GPS nécessite HTTPS (ou localhost).");
                    return;
                }
                const pos = await new Promise((resolve, reject)=>{
                    navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy:true, timeout:12000, maximumAge:0 });
                });
                gps.lat = pos.coords.latitude;
                gps.lon = pos.coords.longitude;
                gps.accuracy = pos.coords.accuracy ?? null;
                gps.lastAt = Date.now();
            }catch(e){
                setVoteError("Activez la localisation pour valider le vote.");
                return;
            }
        }

        btnVerifyOtp.disabled = true;

        try{
            const res = await fetch(CFG.verifyOtpUrl, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept':'application/json'
                },
                body: JSON.stringify({
                    code_otp: code,
                    latitude: gps.lat,
                    longitude: gps.lon
                })
            });

            const data = await res.json().catch(()=>({}));
            if(!res.ok || !data.success){
                setVoteError(data.message || 'Validation OTP échouée.');
                btnVerifyOtp.disabled = false;
                lockButtonsByGPS();
                return;
            }

            setVoteSuccess(data.message || 'Vote enregistré avec succès !');
            toast('✅ Vote enregistré');

        }catch(err){
            console.error(err);
            setVoteError('Erreur réseau. Réessayez.');
            btnVerifyOtp.disabled = false;
            lockButtonsByGPS();
        }
    });
}

/* ====================== INIT ====================== */
document.addEventListener('DOMContentLoaded', function () {
    updateGpsUI();
    lockButtonsByGPS();

    const input = $('search-input');
    if(!input) return;

    const form = input.closest('form');
    if(!form) return;

    let timer = null;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 450);
    });
});
</script>

</body>
</html>
