<a href="{{ route('admin.dashboard') }}" class="h-16 flex items-center px-5 border-b border-white/10 shrink-0">
    <img src="{{ asset('images/aquasecure_logo_dark.svg') }}" alt="AquaSecure" class="h-7 w-auto">
</a>

<nav class="flex-1 overflow-y-auto sidebar-scroll py-3">
    @foreach ($navLinks as $link)
        @continue(! \Illuminate\Support\Facades\Route::has($link['route']))
        @continue(($link['adminOnly'] ?? false) && auth()->user()->role !== 'admin')
        <a href="{{ route($link['route']) }}"
           x-on:click="sidebarOpen = false"
           class="relative flex items-center gap-3 mx-2 my-0.5 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs($link['pattern']) ? 'bg-primary/15 text-primary-light' : 'text-white/65 hover:text-white hover:bg-white/5' }}">
            @if (request()->routeIs($link['pattern']))
                <span class="absolute -left-2 top-1.5 bottom-1.5 w-0.5 bg-primary rounded-full"></span>
            @endif
            <x-dynamic-component :component="'icon.'.$link['icon']" class="w-5 h-5 shrink-0" />
            <span class="truncate">{{ $link['label'] }}</span>
        </a>
    @endforeach
</nav>

<form method="POST" action="{{ route('logout') }}" class="border-t border-white/10 p-2 shrink-0">
    @csrf
    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/65 hover:text-white hover:bg-white/5">
        <x-icon.logout class="w-5 h-5 shrink-0" />
        Déconnexion
    </button>
</form>
