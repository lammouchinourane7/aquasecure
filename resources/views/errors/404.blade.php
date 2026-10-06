<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page introuvable &middot; AquaSecure</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/aquasecure_icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600&family=Barlow+Semi+Condensed:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-bg text-ink min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-md text-center">
        <img src="{{ asset('images/aquasecure_icon.svg') }}" alt="" class="w-14 h-14 rounded-xl mx-auto mb-6">

        <p class="font-display text-sm font-semibold text-primary-strong mb-2">Erreur 404</p>
        <h1 class="font-display text-2xl font-bold text-ink mb-3">Page introuvable</h1>
        <p class="text-muted mb-8">La page que vous cherchez n'existe pas ou a été déplacée.</p>

        <a href="/" class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 font-display text-sm font-semibold text-white hover:bg-primary-strong transition">
            Retour à l'accueil
        </a>
    </div>
</body>
</html>
