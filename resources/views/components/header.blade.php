<header class="w-full p-6 font-poppins">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
        <!-- Logo -->
        <a href="{{ route('vote.index') }}" class="mb-4 md:mb-0">
            <img src="{{ asset('images/LogoGova.jpeg') }}" alt="Logo GovAthon" class="h-16">
        </a>
        

        <!-- Navigation -->
        <nav class="flex justify-center items-center space-x-8 text-lg">
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

            <a href="#" class="font-semibold text-gray-300 hover:text-emerald-300 transition-colors duration-300 [text-shadow:_0_1px_10px_rgb(255_255_255_/_30%)]">À Propos</a>
        </nav>
    </div>
    {{-- Ligne de séparation fine et lumineuse --}}
    <div class="mt-4 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>
</header>