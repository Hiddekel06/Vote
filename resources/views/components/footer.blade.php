
<footer class="
    w-full mt-auto
    bg-black/80 backdrop-blur-sm 
    text-gray-300">
    {{-- Ligne de séparation fine et lumineuse --}}
    <div class="h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>

    <!-- Sponsors horizontal strip (placed above links) -->
    <div class="mx-auto px-4 sm:px-6 md:px-10 pt-8 pb-6 max-w-5xl">
        <h3 class="text-yellow-300 font-bold text-2xl sm:text-3xl md:text-4xl mb-5 text-center tracking-wider [text-shadow:_0_2px_10px_rgb(250_204_21_/_40%)]">Sponsors</h3>
        <div id="sponsors-scroll" class="overflow-x-auto scrollbar-thin scrollbar-thumb-yellow-500/50 scrollbar-track-transparent">
            <div id="sponsors-track" class="flex items-center justify-start gap-6 sm:gap-8 md:gap-10 py-4">
                              <!-- SPONSORS IMAGES -->
                <img src="{{ asset('images/sponsors/DDD.jpg') }}" alt="DDD" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                <img src="{{ asset('images/sponsors/GIZ.png') }}" alt="GIZ" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                <img src="{{ asset('images/sponsors/SAR.png') }}" alt="SAR" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                <img src="{{ asset('images/sponsors/MFP.PNG') }}" alt="MFP" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                <img src="{{ asset('images/sponsors/PAMA.jpg') }}" alt="PAMA" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                <img src="{{ asset('images/sponsors/SAR.png') }}" alt="SAR" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                <img src="{{ asset('images/sponsors/DIMENSION.jpg') }}" alt="DIMENSION" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                <img src="{{ asset('images/sponsors/EXPERTISEFRANCE.png') }}" alt="EXPERTISEFRANCE" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                <img src="{{ asset('images/sponsors/ODS.png') }}" alt="ODS" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                <img src="{{ asset('images/sponsors/MCTN.png') }}" alt="MCTN" class="h-12 sm:h-16 md:h-20 opacity-90 hover:opacity-100 transition-opacity" loading="lazy">
                
            </div>
        </div>

    </div>

    <style>
        /* Hide scrollbar while keeping horizontal scroll */
        #sponsors-scroll {
            -ms-overflow-style: none; /* IE/Edge */
            scrollbar-width: none;    /* Firefox */
        }
        #sponsors-scroll::-webkit-scrollbar {
            display: none;           /* Chrome/Safari */
        }
    </style>

    <script>
        (function() {
            const scroller = document.getElementById('sponsors-scroll');
            const track = document.getElementById('sponsors-track');
            if (!scroller || !track) return;

            // Duplique le contenu pour un flux continu
            track.insertAdjacentHTML('beforeend', track.innerHTML);
            const halfWidth = track.scrollWidth / 2;

            const step = 1; // pixels par tick
            const interval = 20; // ms
            let timer = null;

            const tick = () => {
                scroller.scrollLeft += step;
                if (scroller.scrollLeft >= halfWidth) {
                    scroller.scrollLeft -= halfWidth; // reboucle sans saut
                }
            };

            const start = () => {
                if (timer) return;
                timer = setInterval(tick, interval);
            };

            const stop = () => {
                if (timer) {
                    clearInterval(timer);
                    timer = null;
                }
            };

            scroller.addEventListener('mouseenter', stop);
            scroller.addEventListener('mouseleave', start);

            // Démarre au chargement
            start();
        })();
    </script>

    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 py-10 px-10 mt-2">

        <!-- Copyright -->
        <div class="text-center md:text-left">
            <h3 class="text-yellow-400 font-semibold text-lg mb-2">Gov'Athon</h3>
            <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Gov'Athon. Tous droits réservés.</p>
        </div>

        <!-- Accès rapides -->
        <div>
            <h3 class="text-yellow-400 font-semibold text-lg mb-2">Infos</h3>
            <ul class="space-y-1 text-sm">
                <li><a href="#" class="hover:text-white transition-colors">Infos</a></li>
                <li>52, Vincens x Abdou Karim BOURGI</li>
                <li>Email : <a href="mailto:contact@govathon.sn" class="hover:text-white transition-colors">contact@govathon.sn</a></li>
                <li>Phone : <a href="tel:+221338396600" class="hover:text-white transition-colors">+221 33 839 66 00</a></li>
            </ul>
        </div>

        <!-- Liens utiles -->
        <div>
            <h3 class="text-yellow-400 font-semibold text-lg mb-2">Liens utiles</h3>
            <ul class="space-y-1 text-sm">
             <li><a href="https://www.fonctionpublique.gouv.sn" target="_blank" class=" hover:text-emerald-300 transition-colors">Ministère de la Fonction Publique, du Travail et de la Réforme du Service public</a></li>
                <li><a href="https://www.mctn.sn/" target="_blank" class=" hover:text-emerald-300 transition-colors">Ministère de la Communication, des Télécommunications et du Numérique</a></li>

            </ul>
        </div>

    </div>

    
</footer>

