<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votez pour votre projet préféré - GovAthon</title>

    <!-- Alpine.js et Tailwind -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Police Google "Poppins" -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <style>
        html, body {
            background-color: #000000; /* Noir pur */
            scroll-behavior: smooth;
        }
        .bg-image-custom {
            background-image: url('{{ asset('images/logoBG.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-black text-white bg-image-custom font-poppins flex flex-col min-h-screen">

    <!-- Header -->
    <x-header />

    <!-- Main content -->
    <main class="flex-grow px-4 py-20 text-center max-w-4xl mx-auto flex items-center justify-center">
        <div class="space-y-10">
            <p class="text-white text-2xl sm:text-3xl font-bold opacity-70 transition-opacity duration-400">
                Le futur commence ici
            </p>
            <img src="{{ asset('images/LogoGova.jpeg') }}" alt="Logo GovAthon" class="h-24 mx-auto">
            <a href="{{ route('vote.secteurs') }}" class="inline-block text-2xl sm:text-3xl font-bold [text-shadow:_0_0_15px_rgb(52_211_153_/_70%)] 
                      hover:scale-110 transition-all duration-300"
               style="background: linear-gradient(to right, #22c55e, #fef08a); 
                      -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
               - Faites votre choix -
            </a>
        </div>

        <!-- Ajoute plus de contenu ici si nécessaire pour que le scroll fonctionne -->
    </main>

    <!-- Footer -->
    <x-footer />

</body>
</html>
