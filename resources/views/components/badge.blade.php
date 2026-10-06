@props(['color' => 'neutral'])

@php
    // Badge d'état : pastille de couleur + libellé, sur fond teinté
    $styles = [
        'primary' => ['bg-primary/12 text-primary-strong', 'bg-primary'],
        'alert' => ['bg-alert/12 text-alert-strong', 'bg-alert'],
        'warning' => ['bg-warning/12 text-warning-strong', 'bg-warning'],
        'success' => ['bg-success/12 text-success-strong', 'bg-success'],
        'neutral' => ['bg-ink/8 text-muted', 'bg-muted/60'],
    ];
    [$fond, $pastille] = $styles[$color] ?? $styles['neutral'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-[12.5px] font-medium whitespace-nowrap '.$fond]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $pastille }}" aria-hidden="true"></span>
    {{ $slot }}
</span>
