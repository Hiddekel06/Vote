<!DOCTYPE html> 
<html lang="fr">
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
            <!-- Bloc live polling -->
            <div class="flex items-center justify-between mb-6 gap-3">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/15 text-green-300 text-xs font-semibold uppercase tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Live
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold mt-2">Classement en temps réel</h2>
                </div>
                <div class="text-right text-xs text-gray-400">
                    <div id="live-updated-at">—</div>
                    <div id="live-limit"></div>
                </div>
            </div>

            <div id="live-classement" class="space-y-3 mb-10">
                <div class="text-gray-500 text-sm">Chargement du classement…</div>
            </div>

            <!-- Bouton Retour à l'accueil -->
            <div class="mb-4">
                <a href="{{ route('vote.index') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-yellow-400 transition-colors text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour
                </a>
            </div>

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

            <div x-data="{ tab: '{{ $activeTab }}' }" class="w-full">
                @include('partials.classement-list')
        </div>
    </main>
    <x-footer />

    <script>
        (() => {
            const endpoint = '{{ route('api.classement') }}';
            const container = document.getElementById('live-classement');
            const updatedAtEl = document.getElementById('live-updated-at');
            const limitEl = document.getElementById('live-limit');
            
            let previousData = [];

            function formatDate(dateStr) {
                const d = new Date(dateStr);
                if (Number.isNaN(d.getTime())) return '—';
                return d.toLocaleTimeString('fr-FR', { hour12: false });
            }

            function render(items) {
                if (!container) return;
                if (!items.length) {
                    container.innerHTML = '<div class="text-gray-500 text-sm">Aucun vote pour le moment.</div>';
                    previousData = [];
                    return;
                }

                // Si premier rendu, créer toute la structure
                if (!previousData.length) {
                    const rows = items.map(item => `
                        <div data-id="${item.id}" class="flex items-center justify-between gap-3 rounded-lg border border-white/5 bg-white/5 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="rank w-9 h-9 rounded-full bg-yellow-400/20 text-yellow-300 font-bold flex items-center justify-center">${item.rank}</div>
                                <div>
                                    <div class="font-semibold text-white">${item.nom_projet ?? 'Projet'}</div>
                                    <div class="text-xs text-gray-400">${item.nom_equipe ?? ''}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="votes text-lg font-bold text-yellow-300">${item.votes}</div>
                                <div class="text-xs text-gray-500">votes</div>
                            </div>
                        </div>
                    `).join('');
                    container.innerHTML = rows;
                    previousData = items;
                    return;
                }

                // Comparaison et mise à jour sélective
                items.forEach((item, index) => {
                    const previous = previousData.find(p => p.id === item.id);
                    let rowEl = container.querySelector(`[data-id="${item.id}"]`);

                    // Nouveau projet, l'insérer à la bonne position
                    if (!rowEl) {
                        const newRow = document.createElement('div');
                        newRow.setAttribute('data-id', item.id);
                        newRow.className = 'flex items-center justify-between gap-3 rounded-lg border border-white/5 bg-white/5 px-4 py-3';
                        newRow.innerHTML = `
                            <div class="flex items-center gap-3">
                                <div class="rank w-9 h-9 rounded-full bg-yellow-400/20 text-yellow-300 font-bold flex items-center justify-center">${item.rank}</div>
                                <div>
                                    <div class="font-semibold text-white">${item.nom_projet ?? 'Projet'}</div>
                                    <div class="text-xs text-gray-400">${item.nom_equipe ?? ''}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="votes text-lg font-bold text-yellow-300">${item.votes}</div>
                                <div class="text-xs text-gray-500">votes</div>
                            </div>
                        `;
                        if (index < container.children.length) {
                            container.insertBefore(newRow, container.children[index]);
                        } else {
                            container.appendChild(newRow);
                        }
                        rowEl = newRow;
                    }

                    // Vérifier si changements (votes ou rank)
                    if (previous && previous.votes !== item.votes) {
                        const votesEl = rowEl.querySelector('.votes');
                        if (votesEl) {
                            votesEl.textContent = item.votes;
                        }
                    }

                    if (previous && previous.rank !== item.rank) {
                        const rankEl = rowEl.querySelector('.rank');
                        if (rankEl) {
                            rankEl.textContent = item.rank;
                        }
                    }

                    // Vérifier la position dans le DOM
                    const currentIndex = Array.from(container.children).indexOf(rowEl);
                    if (currentIndex !== index) {
                        if (index < container.children.length) {
                            container.insertBefore(rowEl, container.children[index]);
                        } else {
                            container.appendChild(rowEl);
                        }
                    }
                });

                // Supprimer les projets qui ne sont plus dans le top
                const currentIds = items.map(i => i.id);
                previousData.forEach(prev => {
                    if (!currentIds.includes(prev.id)) {
                        const oldRow = container.querySelector(`[data-id="${prev.id}"]`);
                        if (oldRow) oldRow.remove();
                    }
                });

                previousData = items;
            }

            async function poll() {
                try {
                    const res = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const json = await res.json();
                    render(json.data || []);
                    if (updatedAtEl) updatedAtEl.textContent = 'Mise à jour: ' + formatDate(json.fetched_at);
                    if (limitEl) limitEl.textContent = json.limit ? `Top ${json.limit}` : '';
                } catch (e) {
                    console.warn('Live classement error', e);
                    if (container && !previousData.length) {
                        container.innerHTML = '<div class="text-red-300 text-sm">Impossible de rafraîchir le classement.</div>';
                    }
                }
            }

            poll();
            setInterval(poll, 2000);
        })();
    </script>

</body>
</html>