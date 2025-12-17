<!-- Partial: Contenu des onglets (liste + pagination) -->
<div id="classement-list" aria-live="polite">
    <!-- (per-page control moved to pagination footer) -->
    <!-- Contenu des onglets -->
    <div>
        <!-- Classement général (pagination serveur) -->
        <div x-show="tab === 'general'" class="space-y-4">
            @if($classementGeneral->count())
                @foreach ($classementGeneral as $index => $projet)
                    @php $rank = ($classementGeneral->firstItem() ?? 0) + $index; @endphp
                    @include('classement-item', ['projet' => $projet, 'rank' => $rank])
                @endforeach

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 py-3 mt-2">
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                        <p class="mb-0 text-sm text-gray-300">Affichage {{ $classementGeneral->firstItem() }}–{{ $classementGeneral->lastItem() }} sur {{ $classementGeneral->total() }}</p>

                        {{-- Compact per-page control (bottom-left) --}}
                        <div x-data="{ open: false }" class="relative" x-on:keydown.escape="open = false" x-on:click.away="open = false">
                            @php
                                $currentPer = (int) request()->query('per_page', $perPage ?? 5);
                                $opts = [10,15,25];
                            @endphp

                            <button type="button" @click.prevent="open = !open" aria-haspopup="listbox" :aria-expanded="open"
                                    class="inline-flex items-center gap-2 px-2 py-1 rounded-md bg-gray-800 text-gray-200 text-sm hover:bg-gray-700 focus:outline-none focus:ring-1 focus:ring-amber-300">
                                <span class="sr-only">Changer le nombre d'items affichés</span>
                                <span class="font-medium">{{ $currentPer }}</span>
                                <svg class="w-3 h-3 text-gray-400" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M6 8l4 4 4-4"/></svg>
                            </button>

                            <div x-show="open" x-cloak x-transition class="origin-top-left absolute left-0 mt-2 w-36 rounded-md bg-gray-900/90 backdrop-blur-sm border border-white/5 shadow-lg z-20">
                                <ul role="listbox" aria-label="Sélection du nombre d'items" class="py-1">
                                    @foreach($opts as $opt)
                                        @php $url = request()->fullUrlWithQuery(['per_page' => $opt]); @endphp
                                        <li>
                                            <a href="{{ $url }}" role="option" aria-selected="{{ $currentPer === $opt ? 'true' : 'false' }}"
                                               class="block px-3 py-1 text-sm {{ $currentPer === $opt ? 'text-black bg-amber-300 font-semibold' : 'text-gray-200 hover:bg-gray-800/60' }}">
                                                {{ $opt }} 
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <nav class="flex items-center flex-wrap gap-2" role="navigation" aria-label="Pagination">
                        {{-- Prev --}}
                        @if($classementGeneral->previousPageUrl())
                            <a href="{{ $classementGeneral->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-800 hover:bg-gray-700 text-white" aria-label="Page précédente">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M12.293 16.293a1 1 0 0 1-1.414 1.414l-6-6a1 1 0 0 1 0-1.414l6-6a1 1 0 0 1 1.414 1.414L7.414 10l4.879 4.879z" clip-rule="evenodd"/></svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-900 text-gray-600 opacity-50" aria-hidden="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.293 16.293a1 1 0 0 1-1.414 1.414l-6-6a1 1 0 0 1 0-1.414l6-6a1 1 0 0 1 1.414 1.414L7.414 10l4.879 4.879z" clip-rule="evenodd"/></svg>
                            </span>
                        @endif

                        {{-- Pages --}}
                        @php
                            $lastPage = $classementGeneral->lastPage();
                            $current = $classementGeneral->currentPage();
                            $pages = [1, $current - 1, $current, $current + 1, $lastPage];
                            $pages = array_values(array_unique(array_filter($pages, fn($p) => $p >= 1 && $p <= $lastPage)));
                            sort($pages);
                        @endphp
                        <ul class="flex items-center ml-1 space-x-2">
                            @for ($i = 0; $i < count($pages); $i++)
                                @if($i > 0 && $pages[$i] - $pages[$i-1] > 1)
                                    <li class="text-gray-500 text-sm">…</li>
                                @endif
                                @php $p = $pages[$i]; $url = $classementGeneral->url($p); $isActive = $current == $p; @endphp
                                <li>
                                    <a href="{{ $url }}" class="px-3 py-1 rounded-md text-sm {{ $isActive ? 'bg-yellow-400 text-black' : 'bg-gray-800 text-white hover:bg-gray-700' }}" @if($isActive) aria-current="page" @endif>{{ $p }}</a>
                                </li>
                            @endfor
                        </ul>

                        {{-- Next --}}
                        @if($classementGeneral->nextPageUrl())
                            <a href="{{ $classementGeneral->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-800 hover:bg-gray-700 text-white ml-3" aria-label="Page suivante">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.707 3.707a1 1 0 0 0-1.414-1.414l-6 6a1 1 0 0 0 0 1.414l6 6a1 1 0 0 0 1.414-1.414L3.414 10l4.293-4.293z" clip-rule="evenodd"/></svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-900 text-gray-600 opacity-50 ml-3" aria-hidden="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.707 3.707a1 1 0 0 0-1.414-1.414l-6 6a1 1 0 0 0 0 1.414l6 6a1 1 0 0 0 1.414-1.414L3.414 10l4.293-4.293z" clip-rule="evenodd"/></svg>
                            </span>
                        @endif
                    </nav>
                </div>
            @else
                <div class="text-center p-12 text-gray-400">
                    @if(request('search'))
                        <p class="text-xl mb-4">Aucun projet trouvé pour "{{ request('search') }}"</p>
                        <a href="{{ url()->current() }}" class="inline-block px-6 py-2 rounded-lg border-2 border-emerald-400 text-emerald-400 font-semibold hover:bg-emerald-400/10 transition-colors">
                            Revenir à la liste complète
                        </a>
                    @else
                        <p class="text-xl">Aucun projet n'a encore reçu de vote.</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Classement par catégorie -->
        @foreach($categories as $categorie)
            <div x-show="tab === '{{ $categorie->slug }}'" class="space-y-4" style="display: none;">
                @php $pag = $classementsParCategorie[$categorie->slug]; @endphp
                @if($pag->count())
                    @foreach($pag as $index => $projet)
                        @php $rank = ($pag->firstItem() ?? 0) + $index; @endphp
                        @include('classement-item', ['projet' => $projet, 'rank' => $rank])
                    @endforeach

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 py-3 mt-2">
                        <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                                <p class="mb-0 text-sm text-gray-300">Affichage {{ $pag->firstItem() }}–{{ $pag->lastItem() }} sur {{ $pag->total() }}</p>

                                {{-- Compact per-page control (bottom-left for category) --}}
                                <div x-data="{ open: false }" class="relative" x-on:keydown.escape="open = false" x-on:click.away="open = false">
                                    @php
                                        $currentPer = (int) request()->query('per_page', $perPage ?? 5);
                                        $opts = [10,15,25];
                                    @endphp

                                    <button type="button" @click.prevent="open = !open" aria-haspopup="listbox" :aria-expanded="open"
                                            class="inline-flex items-center gap-2 px-2 py-1 rounded-md bg-gray-800 text-gray-200 text-sm hover:bg-gray-700 focus:outline-none focus:ring-1 focus:ring-amber-300">
                                        <span class="sr-only">Changer le nombre d'items affichés</span>
                                        <span class="font-medium">{{ $currentPer }}</span>
                                        <svg class="w-3 h-3 text-gray-400" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M6 8l4 4 4-4"/></svg>
                                    </button>

                                    <div x-show="open" x-cloak x-transition class="origin-top-left absolute left-0 mt-2 w-36 rounded-md bg-gray-900/90 backdrop-blur-sm border border-white/5 shadow-lg z-20">
                                        <ul role="listbox" aria-label="Sélection du nombre d'items" class="py-1">
                                            @foreach($opts as $opt)
                                                @php $url = request()->fullUrlWithQuery(['per_page' => $opt]); @endphp
                                                <li>
                                                    <a href="{{ $url }}" role="option" aria-selected="{{ $currentPer === $opt ? 'true' : 'false' }}"
                                                       class="block px-3 py-1 text-sm {{ $currentPer === $opt ? 'text-black bg-amber-300 font-semibold' : 'text-gray-200 hover:bg-gray-800/60' }}">
                                                        {{ $opt }} / page
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <nav class="flex items-center flex-wrap gap-2" role="navigation" aria-label="Pagination">
                            @if($pag->previousPageUrl())
                                <a href="{{ $pag->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-800 hover:bg-gray-700 text-white" aria-label="Page précédente">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M12.293 16.293a1 1 0 0 1-1.414 1.414l-6-6a1 1 0 0 1 0-1.414l6-6a1 1 0 0 1 1.414 1.414L7.414 10l4.879 4.879z" clip-rule="evenodd"/></svg>
                                </a>
                            @else
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-900 text-gray-600 opacity-50" aria-hidden="true">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.293 16.293a1 1 0 0 1-1.414 1.414l-6-6a1 1 0 0 1 0-1.414l6-6a1 1 0 0 1 1.414 1.414L7.414 10l4.879 4.879z" clip-rule="evenodd"/></svg>
                                </span>
                            @endif

                            @php
                                $lastPageCat = $pag->lastPage();
                                $currentCat = $pag->currentPage();
                                $pagesCat = [1, $currentCat - 1, $currentCat, $currentCat + 1, $lastPageCat];
                                $pagesCat = array_values(array_unique(array_filter($pagesCat, fn($p) => $p >= 1 && $p <= $lastPageCat)));
                                sort($pagesCat);
                            @endphp
                            <ul class="flex items-center ml-1 space-x-2">
                                @for ($i = 0; $i < count($pagesCat); $i++)
                                    @if($i > 0 && $pagesCat[$i] - $pagesCat[$i-1] > 1)
                                        <li class="text-gray-500 text-sm">…</li>
                                    @endif
                                    @php $p = $pagesCat[$i]; $url = $pag->url($p); $isActive = $currentCat == $p; @endphp
                                    <li>
                                        <a href="{{ $url }}" class="px-3 py-1 rounded-md text-sm {{ $isActive ? 'bg-yellow-400 text-black' : 'bg-gray-800 text-white hover:bg-gray-700' }}" @if($isActive) aria-current="page" @endif>{{ $p }}</a>
                                    </li>
                                @endfor
                            </ul>

                            @if($pag->nextPageUrl())
                                <a href="{{ $pag->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-800 hover:bg-gray-700 text-white ml-3" aria-label="Page suivante">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.707 3.707a1 1 0 0 0-1.414-1.414l-6 6a1 1 0 0 0 0 1.414l6 6a1 1 0 0 0 1.414-1.414L3.414 10l4.293-4.293z" clip-rule="evenodd"/></svg>
                                </a>
                            @else
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-900 text-gray-600 opacity-50 ml-3" aria-hidden="true">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.707 3.707a1 1 0 0 0-1.414-1.414l-6 6a1 1 0 0 0 0 1.414l6 6a1 1 0 0 0 1.414-1.414L3.414 10l4.293-4.293z" clip-rule="evenodd"/></svg>
                                </span>
                            @endif
                        </nav>
                    </div>
                @else
                    <div class="text-center p-12 text-gray-400">
                        @if(request('search'))
                            <p class="text-xl mb-4">Aucun projet trouvé pour "{{ request('search') }}" dans cette catégorie.</p>
                            <a href="{{ url()->current() }}" class="inline-block px-6 py-2 rounded-lg border-2 border-emerald-400 text-emerald-400 font-semibold hover:bg-emerald-400/10 transition-colors">
                                Revenir à la liste complète
                            </a>
                        @else
                            <p class="text-xl">Aucun projet n'a encore reçu de vote dans cette catégorie.</p>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
