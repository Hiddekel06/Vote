<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos - GovAthon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { poppins: ['Poppins', 'sans-serif'] },
                },
            },
        }
    </script>
    <style>
        html, body {
            background-color: #000000; /* Noir pur */
        }
        .bg-image-custom {
           
        }
    </style>
</head>
<body class="bg-black text-white bg-image-custom font-poppins flex flex-col min-h-screen antialiased">

    <x-header />

    <main class="flex-grow container mx-auto px-4 py-12 flex items-center">
        <div class="bg-black/70 backdrop-blur-sm border border-white/10 p-8 sm:p-12 rounded-xl shadow-2xl w-full max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
                <!-- Colonne de texte à gauche -->
                <div class="text-left">
                    <h1 class="text-3xl sm:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-amber-500 mb-4">
                        Notre Vision : Sénégal 2050
                    </h1>
                    <p class="text-gray-300 text-lg mb-6">
                        Le GovAthon est plus qu'une compétition ; c'est un mouvement pour catalyser l'innovation et construire le Sénégal de demain. Nous croyons au pouvoir de la technologie pour transformer les services publics et améliorer la vie de chaque citoyen.
                    </p>
                    <p class="text-gray-400">
                        Ensemble, nous co-créons des solutions audacieuses, transparentes et efficaces, jetant les bases d'une administration moderne et d'un futur prospère pour tous. Rejoignez-nous dans cette aventure vers Sénégal 2050.
                    </p>
                </div>
                <!-- Colonne d'image à droite -->
                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/PR.jpg') }}" alt="Vision du GovAthon" class="rounded-lg shadow-lg object-cover w-full h-auto max-h-96">
                </div>
            </div>
        </div>
    </main>

    <x-footer />

</body>
</html>
