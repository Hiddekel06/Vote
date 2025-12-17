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
        $rankColor = 'text-gray-300';
        $cardBg = 'bg-gradient-to-br from-gray-700/50 via-gray-900/60 to-gray-900/60';
        $borderHover = 'hover:border-gray-400/50';
    } elseif ($rank == 3) {
        $rankColor = 'text-orange-400';
        $cardBg = 'bg-gradient-to-br from-orange-900/50 via-gray-900/60 to-gray-900/60';
        $borderHover = 'hover:border-orange-400/50';
    }
@endphp

@php
    // Format votes: affiche le nombre complet sans notation K
    $votes = (int) ($projet->votes_count ?? 0);
    $displayVotes = number_format($votes, 0, ',', ' ');
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
            // Déterminer l'école/type selon le profil
            $schoolOrType = null;
            $isStudent = false;
            
            if ($projet->submission?->profile_type === 'student' && $projet->listePreselectionne?->snapshot) {
                // Pour les étudiants: extraire l'école
                $isStudent = true;
                $snapshot = json_decode($projet->listePreselectionne->snapshot, true);
                
                if (isset($snapshot['champs_personnalises'])) {
                    $champsPerso = is_string($snapshot['champs_personnalises']) 
                        ? json_decode($snapshot['champs_personnalises'], true) 
                        : $snapshot['champs_personnalises'];
                    
                    $schoolValue = $champsPerso['student_school'] ?? null;
                    if ($schoolValue === 'OTHER') {
                        $schoolOrType = $champsPerso['student_school_other'] ?? 'Autre';
                    } else {
                        $schoolOrType = $schoolValue ?? 'Autre';
                    }
                }
            } elseif ($projet->submission?->profile_type === 'startup') {
                // Pour les startups
                $schoolOrType = 'Startup';
            } else {
                // Par défaut pour les autres
                $schoolOrType = 'Autre';
            }
        @endphp
        
        @if($schoolOrType)
            @if($isStudent)
                <p class="text-sm text-yellow-300 font-semibold truncate mt-1">Ecole: {{ $schoolOrType }}</p>
            @else
                <p class="text-sm text-yellow-300 font-semibold truncate mt-1">{{ $schoolOrType }}</p>
            @endif
        @endif
    </div>

    {{-- Nombre de votes --}}
    <div class="flex-none w-full sm:w-28 text-left sm:text-right pl-0 sm:pl-4">
        <p class="text-xl sm:text-2xl font-bold text-emerald-400" style="text-shadow: 0 0 8px rgba(52, 211, 153, 0.4);" title="{{ number_format($projet->votes_count, 0, ',', ' ') }} votes">{{ $displayVotes }}</p>
        <p class="text-xs text-gray-500 uppercase tracking-wider">Votes</p>
    </div>
</div>