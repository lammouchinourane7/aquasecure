@props(['icon' => null, 'title', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-16 px-6']) }}>
    <div class="w-14 h-14 rounded-full bg-bg flex items-center justify-center text-muted mb-4">
        @if ($icon)
            <x-dynamic-component :component="'icon.'.$icon" class="w-6 h-6" />
        @else
            <x-icon.inbox class="w-6 h-6" />
        @endif
    </div>

    <p class="font-display text-base font-semibold text-ink mb-1">{{ $title }}</p>

    @if ($description)
        <p class="text-sm text-muted max-w-sm mb-5">{{ $description }}</p>
    @endif

    @isset($action)
        {{ $action }}
    @endisset
</div>
