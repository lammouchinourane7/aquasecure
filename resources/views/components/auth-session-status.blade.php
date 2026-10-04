@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg bg-success/12 border border-success/30 px-4 py-3 text-sm font-medium text-success-strong']) }}>
        {{ $status }}
    </div>
@endif
