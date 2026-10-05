@props(['color' => 'neutral'])

@php
    $styles = [
        'primary' => 'bg-primary/12 text-primary-strong',
        'alert' => 'bg-alert/12 text-alert-strong',
        'warning' => 'bg-warning/12 text-warning-strong',
        'success' => 'bg-success/12 text-success-strong',
        'neutral' => 'bg-ink/8 text-muted',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold '.($styles[$color] ?? $styles['neutral'])]) }}>
    {{ $slot }}
</span>
