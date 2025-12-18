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
    <title>À Propos - GovAthon</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-black text-white bg-image-custom font-poppins flex flex-col min-h-screen antialiased">

    <x-header />

    <main x-data="{}" x-init="$nextTick(() => { document.querySelectorAll('[x-animate]').forEach(el => el.classList.remove('opacity-0', 'translate-y-4')) })" class="flex-grow container mx-auto px-4 py-12 flex items-center">
        <div class="bg-black/70 backdrop-blur-sm border border-white/10 p-8 sm:p-12 rounded-xl shadow-2xl w-full max-w-6xl mx-auto space-y-16">
            
            <!-- Section Vision -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <!-- Colonne de texte à gauche -->
                <div x-animate class="text-left opacity-0 translate-y-4 transition-all duration-700 ease-out">
                    <h1 class="text-3xl sm:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-amber-500 mb-4">
                        Notre Vision : Sénégal 2050
                    </h1>
                    <p class="text-gray-300 text-lg mb-6">
                        L'Administration doit agir à tous les niveaux de façon plus accueillante et plus efficace pour les usagers du service public. Nous devons bannir de nos pratiques les procédures et formalités indues qui altèrent l'efficacité de l'Etat. Dans cet objectif, nous entendons investir massivement dans la digitalisation des services et des procédures administratives.
                    </p>
                </div>
                <!-- Colonne d'image à droite -->
                <div x-animate class="flex items-center justify-center opacity-0 translate-y-4 transition-all duration-700 ease-out delay-200">
                    <img src="{{ asset('images/PR.jpg') }}" alt="Vision du GovAthon" class="rounded-lg shadow-lg object-cover w-full h-auto max-h-96 border-2 border-gray-700">
                </div>
            </div>

            <!-- Section Chiffres Clés -->
    </main>

    <x-footer />

</body>
</html>
