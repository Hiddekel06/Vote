<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement des projets - GovAthon</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            background-color: #000000;
        }
        .bg-image-custom {
            background-image: url('{{ asset('images/logoBG.jpg') }}');
        }
    </style>
</head>

<body class="bg-black text-white bg-image-custom font-poppins flex flex-col min-h-screen antialiased">
    <x-header />

    <main class="flex-grow container mx-auto px-4 py-12">
        <div class="bg-black/70 backdrop-blur-sm border border-white/10 p-6 sm:p-8 rounded-xl shadow-2xl w-full max-w-5xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-amber-500 mb-2">
                    Classement des Projets
                </h1>
                <p class="text-gray-400 text-lg">Tendances des votes en temps réel</p>

                <form action="" method="GET" class="mt-6 max-w-xl mx-auto">
                    <label for="search" class="sr-only">Rechercher un projet ou une équipe</label>
                    <div class="flex items-center gap-3">
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher un projet ou une équipe"
                            class="w-full rounded-lg bg-gray-900/70 border border-gray-700 px-4 py-2 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        >
                        <button type="submit" class="px-4 py-2 rounded-lg text-white font-semibold text-sm hover:bg-yellow-300 transition-colors">Rechercher</button>
                    </div>
                </form>
            </div>

            <div x-data="{ tab: '{{ $activeTab ?? 'general' }}' }" class="w-full">
                <!-- Onglets -->
                <div class="border-b border-gray-700 mb-8">
                    <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                        <a href="#" @click.prevent="tab = 'general'"
                           :class="{ 'border-yellow-400 text-yellow-400': tab === 'general', 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-500': tab !== 'general' }"
                           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            Général
                        </a>
                        @foreach($categories as $categorie)
                        <a href="#" @click.prevent="tab = '{{ $categorie->slug }}'"
                           :class="{ 'border-yellow-400 text-yellow-400': tab === '{{ $categorie->slug }}', 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-500': tab !== '{{ $categorie->slug }}' }"
                           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                            {{ $categorie->nom }}
                        </a>
                        @endforeach
                    </nav>
                </div>

                @include('partials.classement-list')
            </div>
        </div>
    </main>

    <x-footer />
    
</body>
</html>