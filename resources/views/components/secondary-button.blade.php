<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-border bg-white px-5 py-2.5 font-display text-sm font-semibold text-ink hover:bg-bg focus:outline-none focus:ring-4 focus:ring-primary/15 transition disabled:opacity-50']) }}>
    {{ $slot }}
</button>
