@props(['projet', 'rank'])

@php
    $rankColor = 'text-gray-400';
    $cardBg = 'bg-gray-900/60';
    $borderHover = 'hover:border-yellow-400/50';

    if ($rank == 1) {
        $rankColor = 'text-yellow-300';
        $cardBg = 'bg-gradient-to-br from-yellow-900/50 via-gray-900/60 to-gray-900/60';
        $borderHover = 'hover:border-yellow-300';
   
    }
@endphp

@php
    // Format votes: keep raw below 1000, switch to K with 1 decimal (trimmed) from 1000+
    $votes = (int) ($projet->votes_count ?? 0);
    if ($votes >= 1000) {
        $shortVotes = number_format($votes / 1000, $votes >= 10000 ? 0 : 1, ',', ' ');
        $shortVotes = rtrim(rtrim($shortVotes, '0'), ',');
        $displayVotes = $shortVotes . 'k';
    } else {
        $displayVotes = number_format($votes, 0, ',', ' ');
    }
@endphp

<div class="flex flex-col sm:flex-row sm:items-center {{ $cardBg }} p-4 rounded-lg border border-white/10 {{ $borderHover }} transition-all duration-300 shadow-lg min-w-0 gap-3 sm:gap-0">
    <div class="hidden list-meta">
        <span class="product">{{ $projet->nom_projet }}</span>
        <span class="customer">Équipe: {{ $projet->nom_equipe }}</span>
        <span class="rating">{{ $projet->votes_count }}</span>
    </div>
    {{-- Rang --}}
    <div class="flex-none w-16 text-center pr-4">
        <span class="text-3xl font-bold {{ $rankColor }}" style="text-shadow: 0 0 10px currentColor;">#{{ $rank }}</span>
    </div>

    {{-- Informations du projet --}}
    <div class="flex-grow border-l border-gray-700 pl-4 sm:pl-4 border-0 sm:border-l min-w-0">
        <p class="text-lg font-semibold text-white truncate">{{ $projet->nom_projet }}</p>
        <p class="text-sm text-gray-400/80 truncate">Équipe: {{ $projet->nom_equipe }}</p>
        @php
            // Extraire l'école depuis le snapshot si c'est un étudiant
            $school = null;
            
            if ($projet->submission?->profile_type === 'student' && $projet->listePreselectionne?->snapshot) {
                $snapshot = json_decode($projet->listePreselectionne->snapshot, true);
                
                // L'école est dans champs_personnalises
                if (isset($snapshot['champs_personnalises'])) {
                    $champsPerso = is_string($snapshot['champs_personnalises']) 
                        ? json_decode($snapshot['champs_personnalises'], true) 
                        : $snapshot['champs_personnalises'];
                    
                    // Si l'école est "OTHER", utiliser le champ student_school_other
                    $schoolValue = $champsPerso['student_school'] ?? null;
                    if ($schoolValue === 'OTHER') {
                        $school = $champsPerso['student_school_other'] ?? null;
                    } else {
                        $school = $schoolValue;
                    }
                }
            }
        @endphp
        
        @if($school)
            <p class="text-sm text-red-300 font-semibold truncate mt-1">{{ $school }}</p>
        @endif
    </div>

    {{-- Nombre de votes --}}
    <div class="flex-none w-full sm:w-28 text-left sm:text-right pl-0 sm:pl-4">
        <p class="text-xl sm:text-2xl font-bold text-emerald-400" style="text-shadow: 0 0 8px rgba(52, 211, 153, 0.4);" title="{{ number_format($projet->votes_count, 0, ',', ' ') }} votes">{{ $displayVotes }}</p>
        <p class="text-xs text-gray-500 uppercase tracking-wider">Votes</p>
    </div>
</div>