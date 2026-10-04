<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'AquaSecure')) &middot; Administration</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-900 text-gray-100 flex flex-col">
            <div class="h-16 flex items-center px-6 text-lg font-bold border-b border-gray-800">
                AquaSecure Admin
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-4 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.reseaux.index') }}"
                   class="block px-4 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.reseaux.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Réseaux
                </a>
                <a href="{{ route('admin.incidents.index') }}"
                   class="block px-4 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.incidents.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Incidents
                </a>
                <a href="{{ route('admin.projets.index') }}"
                   class="block px-4 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.projets.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Projets
                </a>
                <a href="{{ route('admin.capteurs.index') }}"
                   class="block px-4 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.capteurs.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Capteurs
                </a>
                <a href="{{ route('admin.signalements.index') }}"
                   class="block px-4 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.signalements.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Signalements
                </a>
            </nav>

            <div class="px-4 py-4 border-t border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-sm text-gray-300 hover:text-white">
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center px-6">
                <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Administration')</h1>
            </header>

            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
