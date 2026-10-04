<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accès refusé &middot; AquaSecure</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/aquasecure_icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-bg text-ink min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-md text-center">
        <img src="{{ asset('images/aquasecure_icon.svg') }}" alt="" class="w-14 h-14 rounded-xl mx-auto mb-6">

        <p class="font-display text-sm font-semibold text-alert-strong mb-2">Erreur 403</p>
        <h1 class="font-display text-2xl font-bold text-ink mb-3">Accès refusé</h1>
        <p class="text-muted mb-8">
            @auth
                Votre compte ({{ ucfirst(auth()->user()->role) }}) n'a pas les droits nécessaires pour accéder à cette page.
            @else
                Vous devez être connecté avec un compte autorisé pour accéder à cette page.
            @endauth
        </p>

        <div class="flex items-center justify-center gap-3">
            @auth
                @if (in_array(auth()->user()->role, ['gestionnaire', 'admin'], true) && \Illuminate\Support\Facades\Route::has('admin.dashboard'))
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 font-display text-sm font-semibold text-white hover:bg-primary-strong transition">
                        Retour au tableau de bord
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 font-display text-sm font-semibold text-white hover:bg-primary-strong transition">
                        Retour à l'accueil
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 font-display text-sm font-semibold text-white hover:bg-primary-strong transition">
                    Se connecter
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
