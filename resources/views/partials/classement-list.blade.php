<!-- Partial: Contenu des onglets (liste + pagination) -->
<div id="classement-list" aria-live="polite">
    <!-- Contenu des onglets -->
    <div>
        <!-- Classement général (pagination serveur) -->
        <div x-show="tab === 'general'" class="space-y-4">
            @if($classementGeneral->count())
                @foreach ($classementGeneral as $index => $projet)
                    @php $rank = ($classementGeneral->firstItem() ?? 0) + $index; @endphp
                    @include('classement-item', ['projet' => $projet, 'rank' => $rank])
                @endforeach

                <div class="flex items-center justify-between py-3 mt-2">
                    <div>
                        <p class="mb-0 text-sm text-gray-300">Affichage {{ $classementGeneral->firstItem() }}–{{ $classementGeneral->lastItem() }} sur {{ $classementGeneral->total() }}</p>
                    </div>

                    <nav class="flex items-center" role="navigation" aria-label="Pagination">
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
                        <ul class="flex items-center ml-3 space-x-2">
                            @for($p = 1; $p <= $classementGeneral->lastPage(); $p++)
                                @php $url = $classementGeneral->url($p); $isActive = $classementGeneral->currentPage() == $p; @endphp
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
                    <p class="text-xl">Aucun projet n'a encore reçu de vote.</p>
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

                    <div class="flex items-center justify-between py-3 mt-2">
                        <div>
                            <p class="mb-0 text-sm text-gray-300">Affichage {{ $pag->firstItem() }}–{{ $pag->lastItem() }} sur {{ $pag->total() }}</p>
                        </div>

                        <nav class="flex items-center" role="navigation" aria-label="Pagination">
                            @if($pag->previousPageUrl())
                                <a href="{{ $pag->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-800 hover:bg-gray-700 text-white" aria-label="Page précédente">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M12.293 16.293a1 1 0 0 1-1.414 1.414l-6-6a1 1 0 0 1 0-1.414l6-6a1 1 0 0 1 1.414 1.414L7.414 10l4.879 4.879z" clip-rule="evenodd"/></svg>
                                </a>
                            @else
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-gray-900 text-gray-600 opacity-50" aria-hidden="true">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.293 16.293a1 1 0 0 1-1.414 1.414l-6-6a1 1 0 0 1 0-1.414l6-6a1 1 0 0 1 1.414 1.414L7.414 10l4.879 4.879z" clip-rule="evenodd"/></svg>
                                </span>
                            @endif

                            <ul class="flex items-center ml-3 space-x-2">
                                @for($p = 1; $p <= $pag->lastPage(); $p++)
                                    @php $url = $pag->url($p); $isActive = $pag->currentPage() == $p; @endphp
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
                        <p class="text-xl">Aucun projet n'a encore reçu de vote dans cette catégorie.</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
