@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border border-border bg-white px-3 py-2 text-[14.5px] text-ink placeholder:text-muted/70 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors']) }}>
