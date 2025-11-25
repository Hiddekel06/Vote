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

            <h1
                class="text-3xl sm:text-4xl font-bold text-yellow-400 mb-4 transition-all duration-700"
                :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-5'">

                <span class="typed-text"
                    data-typed-text='["Choisissez une catégorie", "Votez maintenant"]'>
                    Choisissez une catégorie
                </span>
                <span class="typed-cursor typed-cursor--blink" aria-hidden="true">|</span>
            </h1>


            <p class="text-gray-300 mb-10">Sélectionnez une catégorie pour découvrir les projets en compétition.</p>

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
                                <svg class="h-12 w-12 text-yellow-400 group-hover:text-yellow-300 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                                @elseif($categorie->slug === 'startup')
                                {{-- Icône pour Startup (fusée) --}}
                                <svg class="h-12 w-12 text-yellow-400 group-hover:text-yellow-400 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                </svg>
          

                                @else
                                {{-- Icône pour Autre (groupe d'utilisateurs) --}}
                                <svg class="h-12 w-12 text-yellow-400 group-hover:text-yellow-400 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m-7.5-2.962a3.75 3.75 0 0 1 5.25 0m-5.25 0a3.75 3.75 0 0 0-5.25 0M12 15a3.75 3.75 0 0 1-3.75-3.75V4.5a3.75 3.75 0 0 1 7.5 0v6.75A3.75 3.75 0 0 1 12 15z" />
                                </svg>
                                @endif
                            </div>
                            <h2 class="text-2xl font-bold text-white group-hover:text-yellow-200 transition-colors duration-300">
                                {{ $categorie->nom }}
                            </h2>
                            <hr class="my-4 border-gray-400/30">


                            <p class="mt-7 text-gray-400 text-sm leading-relaxed">
                                Explorez les projets qui redéfinissent l'avenir dans le secteur {{ strtolower($categorie->nom) }}.
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

    <!-- Footer -->
    <x-footer />

</body>