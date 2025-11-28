<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Statistiques du Vote') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Boutons d'Exportation -->
            <div class="flex justify-end gap-4 mb-6">
                <a href="{{ route('admin.statistiques.export.csv') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-blue-500 uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">Exporter en CSV</a>
                <a href="{{ route('admin.statistiques.export.pdf') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">Exporter en PDF</a>
            </div>

            <!-- Chiffres Clés -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total des Votes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-400 uppercase">Total des Votes</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-100">{{ $totalVotes }}</p>
                    </div>
                </div>

                <!-- Total des Projets -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-400 uppercase">Projets Participants</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-100">{{ $totalProjets }}</p>
                    </div>
                </div>

                <!-- Projet Gagnant -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-400 uppercase">Projet en Tête</h3>
                        @if($projetGagnant)
                            <p class="mt-2 text-xl font-bold text-indigo-400 truncate">{{ $projetGagnant->nom_projet }}</p>
                            <p class="text-sm text-gray-300">{{ $projetGagnant->votes_count }} vote(s)</p>
                        @else
                            <p class="mt-2 text-lg text-gray-400">N/A</p>
                        @endif
                    </div>
                </div>

                <!-- Projet Perdant -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-400 uppercase">Projet en Retrait</h3>
                         @if($projetPerdant)
                            <p class="mt-2 text-xl font-bold text-red-400 truncate">{{ $projetPerdant->nom_projet }}</p>
                            <p class="text-sm text-gray-300">{{ $projetPerdant->votes_count }} vote(s)</p>
                        @else
                            <p class="mt-2 text-lg text-gray-400">N/A</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Répartition des votes par secteur -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold mb-6">Répartition des Votes par Secteur</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-600">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Secteur
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Nombre de Vote(s)
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Pourcentage du Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-900 divide-y divide-gray-700">
                                @forelse ($votesParSecteur as $secteur)
                                    <tr class="hover:bg-gray-700/40">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ $secteur->nom }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center">{{ $secteur->total_votes }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($totalVotes > 0)
                                                <div class="w-full bg-gray-700 rounded-full h-2.5">
                                                    <div class="bg-indigo-500 h-2.5 rounded-full" style="width: {{ ($secteur->total_votes / $totalVotes) * 100 }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-400">{{ number_format(($secteur->total_votes / $totalVotes) * 100, 2) }} %</span>
                                            @else
                                                0.00 %
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Aucun vote enregistré pour le moment.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            
            <!-- Section du Graphique -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold mb-6">Visualisation des Votes par Secteur</h3>
                    <canvas id="votesParSecteurChart" class="max-h-96"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclusion de la bibliothèque Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('votesParSecteurChart').getContext('2d');
            
            // On récupère les données passées par le contrôleur
            const labels = @json($secteurLabels);
            const data = @json($secteurData);

            new Chart(ctx, {
                type: 'bar', // Type de graphique : barres
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nombre de Votes',
                        data: data,
                        backgroundColor: 'rgba(129, 140, 248, 0.5)', // Indigo-400 semi-transparent
                        borderColor: 'rgba(129, 140, 248, 1)', // Indigo-400 plein
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#9ca3af', // gray-400
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                color: '#9ca3af' // gray-400
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false, // On cache la légende car le titre est clair
                            labels: {
                                color: '#9ca3af' // gray-400
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>