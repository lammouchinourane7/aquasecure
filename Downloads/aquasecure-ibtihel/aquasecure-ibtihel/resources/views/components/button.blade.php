@props(['variant' => 'primary', 'href' => null, 'type' => 'submit'])

@php
    $styles = [
        'primary' => 'bg-primary text-white hover:bg-primary-strong focus:ring-primary/30',
        'secondary' => 'bg-white text-ink border border-border hover:bg-bg focus:ring-primary/20',
        'danger' => 'bg-alert-strong text-white hover:bg-alert focus:ring-alert/30',
        'ghost' => 'text-muted hover:text-ink focus:ring-primary/20',
    ];

    $base = 'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 font-display text-sm font-semibold transition focus:outline-none focus:ring-4 disabled:opacity-50 disabled:pointer-events-none';
    $classes = $base.' '.($styles[$variant] ?? $styles['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
