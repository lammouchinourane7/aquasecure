@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border border-border bg-white px-4 py-2.5 text-sm text-ink focus:border-primary focus:ring-4 focus:ring-primary/15 transition']) }}>
    {{ $slot }}
</select>
