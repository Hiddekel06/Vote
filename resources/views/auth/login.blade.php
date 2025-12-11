<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Connexion - GovAthon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body{font-family: 'Poppins', sans-serif;}</style>
</head>
<body class="text-white min-h-screen flex items-center justify-center relative" style="background-image: url({{ asset('images/background.jpg') }}); background-size:cover; background-position:center; background-repeat:no-repeat;">
    <div class="absolute inset-0 bg-black/60" aria-hidden="true"></div>
    <div class="relative z-10 w-full max-w-4xl mx-auto px-4">
        <div class="bg-black/80 border border-white/10 rounded-2xl overflow-hidden shadow-2xl grid grid-cols-1 md:grid-cols-2">
            <!-- Left: illustration / image (you will provide) -->
            <div class="hidden md:block bg-cover bg-center" style="background-image: url({{ asset('images/StarBlack.jpg') }}); min-height:420px;">
                <!-- image provided by user; currently a placeholder path -->
            </div>

            <!-- Right: form -->
            <div class="p-8 md:p-12">
                <div class="mb-6 text-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center mb-4">
                        <img src="{{ asset('images/LogoGova.jpeg') }}" alt="GovAthon" class="w-32 h-32 object-contain" />
                    </a>
                    <h1 class="text-2xl font-bold text-white">Connexion</h1>
                    <p class="text-gray-400 mt-1">Connectez-vous pour accéder à l'administration</p>
                </div>

                @if (session('status'))
                    <div class="mb-4 text-sm text-green-400">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-200">Adresse e-mail</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full bg-gray-900 border border-white/10 rounded-md px-3 py-2 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        @error('email')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-200">Mot de passe</label>
                        <input id="password" name="password" type="password" required class="mt-1 block w-full bg-gray-900 border border-white/10 rounded-md px-3 py-2 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                        @error('password')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center text-sm text-gray-300">
                            <input type="checkbox" name="remember" class="rounded text-yellow-400 bg-gray-800 border-gray-700" />
                            <span class="ml-2">Se souvenir de moi</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-gray-400 hover:text-white">Mot de passe oublié ?</a>
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-yellow-400 text-black font-semibold px-4 py-2 rounded-md hover:opacity-95">Se connecter</button>
                    </div>
                </form>

                <div class="mt-6 text-center text-sm text-gray-400">
                    <span>Pas de compte ?</span>
                    @if (Route::has('register'))
                        <a href="{{ route('login') }}" class="text-yellow-400 ml-1">Créer un compte</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
