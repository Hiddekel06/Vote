@props(['projet', 'rank'])

@php
    $rankColor = 'text-gray-400';
    $cardBg = 'bg-gray-900/60';
    $borderHover = 'hover:border-yellow-400/50';

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
