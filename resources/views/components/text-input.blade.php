@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border border-border bg-white px-4 py-2.5 text-sm text-ink placeholder:text-muted/70 focus:border-primary focus:ring-4 focus:ring-primary/15 transition']) }}>
