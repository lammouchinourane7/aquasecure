@php
    $flashes = [];
    if (session('success')) {
        $flashes[] = ['type' => 'success', 'message' => session('success')];
    }
    if (session('error')) {
        $flashes[] = ['type' => 'alert', 'message' => session('error')];
    }
@endphp

@if (count($flashes))
    <div class="fixed top-4 right-4 z-50 space-y-2 w-full max-w-sm">
        @foreach ($flashes as $flash)
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 4000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="flex items-start gap-3 rounded-lg border px-4 py-3 shadow-sm bg-white {{ $flash['type'] === 'success' ? 'border-success/30' : 'border-alert/30' }}">
                @if ($flash['type'] === 'success')
                    <svg class="w-5 h-5 shrink-0 text-success-strong" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                @else
                    <svg class="w-5 h-5 shrink-0 text-alert-strong" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                @endif
                <p class="text-sm font-medium text-ink">{{ $flash['message'] }}</p>
                <button type="button" x-on:click="show = false" class="ml-auto text-muted hover:text-ink">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
        @endforeach
    </div>
@endif
