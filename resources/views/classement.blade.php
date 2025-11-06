<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement des projets - GovAthon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { poppins: ['Poppins', 'sans-serif'] },
                },
            },
        }
    </script>
    <style>
        html, body {
            background-color: #000000; /* Noir pur */
        }
        .bg-image-custom {
            background-image: url('{{ asset('images/logoBG.jpg') }}');
        }
    </style>
</head>
    
<body class="bg-black text-white bg-image-custom font-poppins flex flex-col min-h-screen antialiased">
    <x-header />

    <main class="flex-grow container mx-auto px-4 py-12 flex items-center">
        <div class="bg-black/70 backdrop-blur-sm border border-white/10 p-6 sm:p-8 rounded-xl shadow-2xl w-full max-w-4xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-amber-500 mb-2" >
                    Classement : <span class="text-white">{{ $categorie->nom }}</span>
                </h1>
                <p class="text-gray-400 text-lg">Tendances des votes en temps réel</p>
            </div>

            <div class="space-y-4">
                @forelse ($projets as $index => $projet)
                    @php
                        $rank = ($projets->currentPage() - 1) * $projets->perPage() + $index + 1;
                        $rankColor = 'text-gray-400';
                        $cardBg = 'bg-gray-900/60';
                        $borderHover = 'hover:border-yellow-400/50';

                        if ($projets->currentPage() == 1) {
                            if ($rank == 1) {
                                $rankColor = 'text-yellow-300';
                                $cardBg = 'bg-gradient-to-br from-yellow-900/50 via-gray-900/60 to-gray-900/60';
                                $borderHover = 'hover:border-yellow-300';
                            } elseif ($rank == 2) {
                                $rankColor = 'text-slate-300';
                                $cardBg = 'bg-gradient-to-br from-slate-800/50 via-gray-900/60 to-gray-900/60';
                                $borderHover = 'hover:border-slate-300';
                            } elseif ($rank == 3) {
                                $rankColor = 'text-amber-500';
                                $cardBg = 'bg-gradient-to-br from-amber-900/50 via-gray-900/60 to-gray-900/60';
                                $borderHover = 'hover:border-amber-500';
                            }
                        }
                    @endphp
                    <div class="flex items-center {{ $cardBg }} p-4 rounded-lg border border-white/10 {{ $borderHover }} transition-all duration-300 shadow-lg">
                        {{-- Rang --}}
                        <div class="flex-none w-16 text-center pr-4">
                            <span class="text-3xl font-bold {{ $rankColor }}" style="text-shadow: 0 0 10px currentColor;">#{{ $rank }}</span>
                        </div>

                        {{-- Informations du projet --}}
                        <div class="flex-grow border-l border-gray-700 pl-4">
                            <p class="text-lg font-semibold text-white">{{ $projet->nom_projet }}</p>
                            <p class="text-sm text-gray-400/80">
                                Équipe: {{ $projet->nom_equipe }} <span class="text-gray-600 mx-2">&bull;</span> Secteur: {{ $projet->secteur->nom }}
                            </p>
                        </div>

                        {{-- Nombre de votes --}}
                        <div class="flex-none w-28 text-right pl-4">
                            <p class="text-2xl font-bold text-emerald-400" style="text-shadow: 0 0 8px rgba(52, 211, 153, 0.4);">{{ number_format($projet->votes_count, 0, ',', ' ') }}</p>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Votes</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-12 text-gray-400">
                        <p class="text-xl">Aucun projet n'a encore reçu de vote vérifié.</p>
                        <p>Le classement apparaîtra ici dès que les premiers votes seront validés.</p>
                    </div>
                @endforelse
            </div>

            {{-- Liens de pagination --}}
            <div class="mt-8">{{ $projets->links() }}</div>
        </div>
    </main>

    <x-footer />
</body>
</html>