<header class="w-full p-3 sm:p-4 md:p-6 font-poppins">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
        <!-- Logo -->
        <a href="{{ route('vote.index') }}" class="mb-3 md:mb-0">
            <img src="{{ asset('images/LogoGova.jpeg') }}" alt="Logo GovAthon" class="h-12 sm:h-14 md:h-16">
        </a>
        
        

        <!-- Navigation -->
        <nav class="flex justify-center items-center space-x-2 sm:space-x-4 md:space-x-8 text-xs sm:text-sm md:text-lg flex-wrap">
            <a href="{{ route('vote.index') }}" 
               class="font-semibold hover:text-emerald-300 transition-colors duration-300 [text-shadow:_0_1px_10px_rgb(255_255_255_/_30%)] 
                      {{ request()->routeIs('vote.index') || request()->routeIs('vote.secteurs') ? 'text-white' : 'text-gray-300' }}">
                Accueil
            </a>
            
            <a href="{{ route('projets.classement') }}" 
               class="font-semibold hover:text-emerald-300 transition-colors duration-300 [text-shadow:_0_1px_10px_rgb(255_255_255_/_30%)]
                      {{ request()->routeIs('projets.classement') ? 'text-white' : 'text-gray-300' }}">
                Classement
            </a>

            <a href="{{ route('apropos') }}"
               class="font-semibold hover:text-emerald-300 transition-colors duration-300 [text-shadow:_0_1px_10px_rgb(255_255_255_/_30%)]
                      {{ request()->routeIs('apropos') ? 'text-white' : 'text-gray-300' }}">
                À Propos</a>
        </nav>
    </div>
    {{-- Ligne de séparation fine et lumineuse --}}
    <div class="mt-4 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>
</header>