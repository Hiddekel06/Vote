<!DOCTYPE html>
<html lang="fr" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé - GovAthon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-black text-white flex flex-col min-h-screen bg-cover bg-center bg-fixed font-poppins overflow-x-hidden">

    <x-header />

    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="bg-black bg-opacity-60 p-8 rounded-lg shadow-2xl max-w-2xl w-full text-center">
            
            <!-- Icône d'erreur -->
            <div class="mb-6 flex justify-center">
                <div class="bg-red-900/30 rounded-full p-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Titre -->
            <h1 class="text-4xl font-bold text-red-400 mb-4">Accès refusé</h1>

            <!-- Message d'erreur personnalisé -->
            <p class="text-xl text-gray-300 mb-8">
                {{ $message ?? 'Vous n\'avez pas accès à cette page. Veuillez scanner le QR Code pour continuer.' }}
            </p>

            <!-- Description détaillée -->
            <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-yellow-400 font-semibold mb-3">Comment procéder :</h2>
                <ol class="text-gray-300 space-y-2 text-sm">
                    <li>1. Localisez le code QR présent sur les écrans de l'événement</li>
                    <li>2. Ouvrez l'appareil photo de votre téléphone</li>
                    <li>3. Scannez le code QR</li>
                    <li>4. Vous serez redirigé automatiquement vers le vote</li>
                </ol>
            </div>

            <!-- Bouton d'accueil -->
            <a href="{{ route('vote.index') }}" class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-8 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Retour à l'accueil
            </a>

            <!-- Footer texte -->
            <p class="text-gray-500 text-sm mt-8">
                Si vous continuez à rencontrer des problèmes, contactez l'organisateur de l'événement.
            </p>

        </div>
    </main>

</body>
</html>
