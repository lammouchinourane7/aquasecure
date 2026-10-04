<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AquaSecure') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('images/aquasecure_icon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            <!-- Brand panel -->
            <div class="hidden lg:flex lg:w-[42%] relative bg-primary flex-col justify-between overflow-hidden p-12">
                <svg class="absolute inset-0 w-full h-full opacity-[0.08]" viewBox="0 0 400 800" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                    <path d="M-20 650 C 80 650 100 580 160 610 C 220 640 260 520 340 480 C 400 450 420 520 460 500"
                          fill="none" stroke="white" stroke-width="3" />
                    <path d="M-40 200 C 60 180 90 260 150 230 C 210 200 240 120 320 150 C 380 173 400 100 460 120"
                          fill="none" stroke="white" stroke-width="3" />
                    <circle cx="160" cy="610" r="6" fill="white" />
                    <circle cx="260" cy="520" r="6" fill="white" />
                    <circle cx="150" cy="230" r="6" fill="white" />
                    <circle cx="320" cy="150" r="6" fill="white" />
                </svg>

                <a href="/" class="relative flex items-center gap-3">
                    <img src="{{ asset('images/aquasecure_icon.svg') }}" alt="" class="w-10 h-10 rounded-xl">
                    <span class="font-display text-2xl font-bold text-white">AquaSecure</span>
                </a>

                <div class="relative">
                    <p class="font-display text-3xl font-semibold text-white leading-tight max-w-sm">
                        La surveillance citoyenne du réseau d'eau potable.
                    </p>
                    <ul class="mt-8 space-y-3 text-sm text-white/80">
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            Suivi en temps réel des réseaux et capteurs
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            Signalement en quelques clics pour chaque citoyen
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            Tableau de bord dédié pour les gestionnaires
                        </li>
                    </ul>
                </div>

                <p class="relative text-xs text-white/60">&copy; {{ date('Y') }} AquaSecure</p>
            </div>

            <!-- Form panel -->
            <div class="flex-1 flex flex-col items-center justify-center bg-bg px-6 py-12">
                <div class="w-full max-w-sm">
                    <a href="/" class="lg:hidden flex items-center justify-center gap-2.5 mb-8">
                        <img src="{{ asset('images/aquasecure_icon.svg') }}" alt="" class="w-9 h-9 rounded-lg">
                        <span class="font-display text-xl font-bold text-ink">AquaSecure</span>
                    </a>

                    <div class="bg-white rounded-xl border border-border p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
