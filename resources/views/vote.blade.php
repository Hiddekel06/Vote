<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votez pour votre projet préféré - GovAthon</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Police Google "Poppins" -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<script src="vendors/typed.js/typed.umd.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const el = document.querySelector(".typed-text");
        const strings = JSON.parse(el.getAttribute("data-typed-text"));

        new Typed(el, {
            strings,
            typeSpeed: 90,
            backSpeed: 40,
            backDelay: 2000,
            loop: true,
        });
    });
</script>

<body class="bg-black text-white bg-image-custom font-poppins flex flex-col min-h-screen">

    <!-- Header -->
    <x-header />

    <!-- Main content -->
    <main class="flex-grow px-4 py-16 text-center max-w-5xl mx-auto flex items-center justify-center">
        {{-- On ajoute un conteneur Alpine.js pour gérer l'état de l'animation --}}
        <div class="w-full" x-data="{ visible: false }" x-init="setTimeout(() => { visible = true }, 100)">

            <!-- Hero Title + Timer (style repris de reservation) -->
            <div class="mb-8">
                <p class="slogan-phrase text-emerald-400">L'innovation par et pour les citoyens</p>
                <p class="banner-phrase">En Route pour la Grande Finale</p>
                <div class="timer-modern">
                    <div class="time-card">
                        <span id="countdown-days">00</span>
                        <small>Jours</small>
                    </div>
                    <div class="time-card">
                        <span id="countdown-hours">00</span>
                        <small>Heures</small>
                    </div>
                    <div class="time-card">
                        <span id="countdown-minutes">00</span>
                        <small>Minutes</small>
                    </div>
                    <div class="time-card">
                        <span id="countdown-seconds">00</span>
                        <small>Secondes</small>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-center gap-2 text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">Cicad ,Diamniadio</span>
                </div>
            </div>

            <h1
                class="text-3xl sm:text-4xl font-bold text-yellow-400 mb-4 transition-all duration-700"
                :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-5'">

                <span class="typed-text"
                    data-typed-text='["Ton avis compte ", "Votez maintenant"]'>
                    La reforme c'est maintenant !
                </span>
                <span class="typed-cursor typed-cursor--blink" aria-hidden="true">|</span>
            </h1>


            <p class="text-gray-300 mb-10">Selectionnez votre projet préféré</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($categories as $index => $categorie)
                {{-- Ajout des classes de transition et de l'attribut de style pour le délai --}}
                <a href="{{ route('vote.secteurs', ['profile_type' => $categorie->slug]) }}"
                    class="group block bg-black/50 backdrop-blur-sm border border-gray-400/30  p-7 transform hover:scale-105 hover:shadow-xl hover:shadow-yellow-400/10 transition-all duration-500"
                    :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                    style="transition-delay: {{ $index * 150 }}ms;">
                    <div class="flex flex-col justify-between h-full text-left">
                        <div class="flex-grow">
                            <div class="mb-4">
                                @if($categorie->slug === 'student')
                                {{-- Icône pour Étudiant (chapeau de diplômé) --}}
                                <svg class="h-12 w-12 text-emerald-400 group-hover:text-emerald-300 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                                @elseif($categorie->slug === 'startup')
                                {{-- Icône pour Startup (fusée) --}}
                                <svg class="h-12 w-12 text-emerald-400 group-hover:text-emerald-300 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                </svg>
                                @else
                                {{-- Icône pour Autre (groupe d'utilisateurs) --}}
                                <svg class="h-12 w-12 text-emerald-400 group-hover:text-emerald-400 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m-7.5-2.962a3.75 3.75 0 0 1 5.25 0m-5.25 0a3.75 3.75 0 0 0-5.25 0M12 15a3.75 3.75 0 0 1-3.75-3.75V4.5a3.75 3.75 0 0 1 7.5 0v6.75A3.75 3.75 0 0 1 12 15z" />
                                </svg>
                                @endif
                            </div>
                            <h2 class="text-2xl font-bold text-white group-hover:text-yellow-200 transition-colors duration-300">
                                {{ $categorie->nom }}
                            </h2>
                            <hr class="my-4 border-gray-400/30">


                            <p class="mt-7 text-gray-400 text-sm leading-relaxed">
                                Explorez les projets qui redéfinissent l'avenir dans la categorie "{{ strtolower($categorie->nom) }}".
                                Une vitrine d'idées audacieuses et de solutions novatrices.
                            </p>
                        </div>
                        <div class="mt-8 ">
                            <p class="text-yellow-400/80 font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Découvrir &rarr;
                            </p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </main>

        <style>
            .slogan-phrase{ text-align:center; color:#00a651; font-weight:600; font-family:'Poppins',sans-serif; letter-spacing:.35px; font-size:clamp(12px,2.8vw,14px); margin-bottom:4px; }
            .banner-phrase{ text-align:center; color:#f7f7f7; font-weight:600; font-family:'Poppins',sans-serif; letter-spacing:.3px; font-size:clamp(13px,3.2vw,16px); margin-bottom:8px; }
            .timer-modern { display:flex; gap:12px; justify-content:center; margin:16px 0; flex-wrap:wrap; }
            .time-card { background:rgba(255,193,7,.08); border:2px solid #ffc107; border-radius:14px; width:clamp(64px,11.5vw,92px); padding:clamp(9px,2.4vw,15px) 7px; text-align:center; box-shadow:0 0 18px rgba(255,193,7,.32); transition:transform .25s ease, box-shadow .25s ease; }
            .time-card:hover { transform:translateY(-3px); box-shadow:0 0 26px rgba(255,193,7,.5); }
            .time-card span { display:block; font-size:clamp(20px,4.8vw,30px); font-weight:800; color:#ffc107; line-height:1; }
            .time-card small { display:block; margin-top:5px; font-size:clamp(9px,2vw,11px); letter-spacing:.7px; text-transform:uppercase; color:rgba(255,255,255,.75); }
            @media (max-width:768px){ .timer-modern{ gap:10px; } }
            @media (max-width:480px){ .timer-modern{ gap:9px; } }
        </style>

    <script>
        // Countdown compatible with reservation design IDs
        (function(){
            const target = new Date(new Date().getFullYear(), 11, 23, 23, 59, 59);
            const elDays = document.getElementById('countdown-days');
            const elHours = document.getElementById('countdown-hours');
            const elMins = document.getElementById('countdown-minutes');
            const elSecs = document.getElementById('countdown-seconds');

            function pad(n){ return String(n).padStart(2,'0'); }
            function tick(){
                const now = new Date();
                let diff = Math.max(0, target - now);
                const secs = Math.floor(diff / 1000);
                const d = Math.floor(secs / 86400);
                const h = Math.floor((secs % 86400) / 3600);
                const m = Math.floor((secs % 3600) / 60);
                const s = secs % 60;
                if (elDays) elDays.textContent = pad(d);
                if (elHours) elHours.textContent = pad(h);
                if (elMins) elMins.textContent = pad(m);
                if (elSecs) elSecs.textContent = pad(s);
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>

    <!-- Footer -->
    <x-footer />

</body>