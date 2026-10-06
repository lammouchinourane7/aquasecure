<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'AquaSecure')) &middot; Administration</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/aquasecure_icon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600&family=Barlow+Semi+Condensed:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-bg text-ink" x-data="{ sidebarOpen: false }">
    <x-flash-message />

    @php
        // Menu regroupé par module, dans l'ordre du cycle de vie d'un réseau
        $navLinks = [
            ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Tableau de bord', 'icon' => 'home', 'group' => null],
            ['route' => 'admin.zones.index', 'pattern' => 'admin.zones.*', 'label' => 'Zones', 'icon' => 'map', 'group' => 'Infrastructure'],
            ['route' => 'admin.reseaux.index', 'pattern' => 'admin.reseaux.*', 'label' => 'Réseaux', 'icon' => 'share', 'group' => 'Infrastructure'],
            ['route' => 'admin.incidents.index', 'pattern' => 'admin.incidents.*', 'label' => 'Incidents', 'icon' => 'alert-triangle', 'group' => 'Exploitation'],
            ['route' => 'admin.interventions.index', 'pattern' => 'admin.interventions.*', 'label' => 'Interventions', 'icon' => 'wrench', 'group' => 'Exploitation'],
            ['route' => 'admin.capteurs.index', 'pattern' => 'admin.capteurs.*', 'label' => 'Capteurs', 'icon' => 'radar', 'group' => 'Surveillance'],
            ['route' => 'admin.releves.index', 'pattern' => 'admin.releves.*', 'label' => 'Relevés', 'icon' => 'chart', 'group' => 'Surveillance'],
            ['route' => 'admin.points.index', 'pattern' => 'admin.points.*', 'label' => 'Points de prélèvement', 'icon' => 'droplet', 'group' => 'Qualité de l\'eau'],
            ['route' => 'admin.analyses.index', 'pattern' => 'admin.analyses.*', 'label' => 'Analyses', 'icon' => 'flask', 'group' => 'Qualité de l\'eau'],
            ['route' => 'admin.projets.index', 'pattern' => 'admin.projets.*', 'label' => 'Projets de rénovation', 'icon' => 'building', 'group' => 'Rénovation'],
            ['route' => 'admin.financements.index', 'pattern' => 'admin.financements.*', 'label' => 'Financements', 'icon' => 'coin', 'group' => 'Rénovation'],
            ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => 'Utilisateurs', 'icon' => 'users', 'group' => 'Administration', 'adminOnly' => true],
        ];
    @endphp

    <!-- Desktop sidebar -->
    <aside class="hidden md:flex fixed inset-y-0 left-0 w-60 bg-conduite flex-col z-30">
        @include('partials.sidebar-nav', ['navLinks' => $navLinks])
    </aside>

    <!-- Mobile sidebar (slide-over) -->
    <div x-show="sidebarOpen" style="display: none;" class="fixed inset-0 z-40 md:hidden">
        <div class="fixed inset-0 bg-conduite/60" x-on:click="sidebarOpen = false" x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <aside x-show="sidebarOpen"
               x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
               class="relative flex flex-col w-64 h-full bg-conduite">
            @include('partials.sidebar-nav', ['navLinks' => $navLinks])
        </aside>
    </div>

    <div class="md:pl-60">
        <header class="sticky top-0 z-10 h-14 bg-white border-b border-border flex items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <button type="button" id="sidebar-toggle" x-on:click="sidebarOpen = true" class="md:hidden text-ink -ml-1 p-1.5">
                    <x-icon.menu />
                </button>

                <nav class="flex items-center gap-1.5 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="text-muted hover:text-ink">Administration</a>
                    <span class="text-border">/</span>
                    <span class="font-medium text-ink">@yield('title', 'Tableau de bord')</span>
                </nav>
            </div>

            <div class="flex items-center gap-4 text-sm">
                <span class="hidden sm:block text-muted">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
                <span class="text-ink/80">
                    {{ auth()->user()->role === 'admin' ? 'Administrateur' : 'Gestionnaire' }}
                </span>
            </div>
        </header>

        <main class="p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
