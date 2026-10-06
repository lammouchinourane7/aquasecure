<a href="{{ route('admin.dashboard') }}" class="h-14 flex items-center px-5 border-b border-white/10 shrink-0">
    <img src="{{ asset('images/aquasecure_logo_dark.svg') }}" alt="AquaSecure" class="h-8 w-auto">
</a>

@php
    // On ne garde que les liens accessibles à l'utilisateur, puis on les regroupe par module
    $groupes = collect($navLinks)
        ->filter(fn ($l) => \Illuminate\Support\Facades\Route::has($l['route']))
        ->reject(fn ($l) => ($l['adminOnly'] ?? false) && auth()->user()->role !== 'admin')
        ->groupBy(fn ($l) => $l['group'] ?? '');
@endphp

<nav class="flex-1 overflow-y-auto sidebar-scroll py-3">
    @foreach ($groupes as $groupe => $liens)
        @if ($groupe !== '')
            <p class="px-5 pt-4 pb-1 text-[12px] font-medium text-white/40">{{ $groupe }}</p>
        @endif
        @foreach ($liens as $link)
            @php $actif = request()->routeIs($link['pattern']); @endphp
            <a href="{{ route($link['route']) }}"
               x-on:click="sidebarOpen = false"
               @if ($actif) aria-current="page" @endif
               class="flex items-center gap-3 px-5 py-2 text-[14px] border-l-[3px] {{ $actif ? 'border-primary-light bg-white/[0.06] text-white font-medium' : 'border-transparent text-white/70 hover:text-white hover:bg-white/[0.04]' }}">
                <x-dynamic-component :component="'icon.'.$link['icon']" class="w-[18px] h-[18px] shrink-0 {{ $actif ? 'text-primary-light' : '' }}" />
                <span class="truncate">{{ $link['label'] }}</span>
            </a>
        @endforeach
    @endforeach
</nav>

<div class="border-t border-white/10 px-5 py-3 shrink-0">
    <p class="text-[13px] text-white/90 truncate">{{ auth()->user()->name }}</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="mt-1 flex items-center gap-2 text-[13px] text-white/55 hover:text-white">
            <x-icon.logout class="w-4 h-4" />
            Se déconnecter
        </button>
    </form>
</div>
