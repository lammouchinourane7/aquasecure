<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'AquaSecure'))</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/aquasecure_icon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-bg text-ink">
    <x-flash-message />

    <header class="fixed top-0 inset-x-0 z-20 bg-white border-b border-border">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/aquasecure_logo.svg') }}" alt="AquaSecure" class="h-8 w-auto">
                </a>

                <nav class="hidden md:flex items-center gap-8 absolute left-1/2 -translate-x-1/2">
                    <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-primary' : 'text-ink/70 hover:text-ink' }}">Accueil</a>
                    <a href="{{ route('reseaux.index') }}" class="text-sm font-medium {{ request()->routeIs('reseaux.*') ? 'text-primary' : 'text-ink/70 hover:text-ink' }}">Réseaux</a>
                    <a href="{{ route('signalements.index') }}" class="text-sm font-medium {{ request()->routeIs('signalements.*') ? 'text-primary' : 'text-ink/70 hover:text-ink' }}">Signaler un problème</a>
                    <a href="{{ route('projets.index') }}" class="text-sm font-medium {{ request()->routeIs('projets.*') ? 'text-primary' : 'text-ink/70 hover:text-ink' }}">Projets</a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" x-on:click="open = !open" x-on:click.outside="open = false"
                                    class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-bg transition">
                                <span class="w-8 h-8 rounded-full bg-primary/12 text-primary-strong flex items-center justify-center font-display text-sm font-semibold">
                                    {{ Str::of(Auth::user()->name)->substr(0, 1)->upper() }}
                                </span>
                                <span class="hidden sm:block text-sm font-medium text-ink">{{ Auth::user()->name }}</span>
                                <x-icon.chevron-down class="w-4 h-4 text-muted" />
                            </button>

                            <div x-show="open" x-transition style="display: none;"
                                 class="absolute right-0 mt-2 w-48 rounded-lg border border-border bg-white py-1.5 shadow-sm">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-ink hover:bg-bg">
                                    <x-icon.user-circle class="w-4 h-4 text-muted" />
                                    Mon profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-ink hover:bg-bg">
                                        <x-icon.logout class="w-4 h-4 text-muted" />
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-ink/70 hover:text-ink px-3 py-2">Connexion</a>
                        <x-button href="{{ route('register') }}" variant="primary">Inscription</x-button>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="pt-16">
        @yield('content')
    </main>

    <footer class="border-t border-border mt-20">
        <div class="max-w-6xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/aquasecure_icon.svg') }}" alt="" class="w-6 h-6 rounded-md">
                <span class="text-sm text-muted">AquaSecure &copy; {{ date('Y') }}</span>
            </div>
            <div class="flex items-center gap-6 text-sm text-muted">
                <a href="#" class="hover:text-ink">Mentions légales</a>
                <a href="#" class="hover:text-ink">Contact</a>
            </div>
        </div>
    </footer>
</body>
</html>
