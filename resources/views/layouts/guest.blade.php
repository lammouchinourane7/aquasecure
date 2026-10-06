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
        <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600&family=Barlow+Semi+Condensed:wght@500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            <!-- Panneau de marque : schéma de réseau stylisé -->
            <div class="hidden lg:flex lg:w-[42%] relative bg-conduite flex-col justify-between overflow-hidden p-12">
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 400 800" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                    <g fill="none" stroke="#FFFFFF" stroke-opacity="0.09" stroke-width="6" stroke-linejoin="round">
                        <path d="M-10 520 H120 V380 H260 V240 H410" />
                        <path d="M120 520 V660 H300 V800" />
                        <path d="M260 380 H410" />
                        <path d="M60 520 V820" />
                    </g>
                    <g fill="none" stroke="#9FD0E2" stroke-opacity="0.45" stroke-width="2.5" stroke-linejoin="round">
                        <path d="M-10 520 H120 V380 H260 V240 H410" />
                    </g>
                    <g fill="#17344A" stroke="#9FD0E2" stroke-opacity="0.6" stroke-width="2.5">
                        <rect x="111" y="511" width="18" height="18" />
                        <rect x="251" y="371" width="18" height="18" />
                        <circle cx="260" cy="240" r="9" />
                    </g>
                </svg>

                <a href="/" class="relative">
                    <img src="{{ asset('images/aquasecure_logo_dark.svg') }}" alt="AquaSecure" class="h-10 w-auto">
                </a>

                <div class="relative max-w-sm">
                    <p class="font-display text-[30px] font-semibold text-white leading-tight">
                        Le réseau d'eau potable, suivi ouvrage par ouvrage.
                    </p>
                    <p class="mt-4 text-[15px] text-white/70 leading-relaxed">
                        Les citoyens consultent l'état des réseaux et la qualité de l'eau de leur zone.
                        Les services des eaux y gèrent incidents, capteurs, analyses et chantiers de rénovation.
                    </p>
                </div>

                <p class="relative text-[13px] text-white/50">AquaSecure &copy; {{ date('Y') }}</p>
            </div>

            <!-- Form panel -->
            <div class="flex-1 flex flex-col items-center justify-center bg-bg px-6 py-12">
                <div class="w-full max-w-sm">
                    <a href="/" class="lg:hidden flex justify-center mb-8">
                        <img src="{{ asset('images/aquasecure_logo.svg') }}" alt="AquaSecure" class="h-9 w-auto">
                    </a>

                    <div class="bg-white rounded-xl border border-border p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
