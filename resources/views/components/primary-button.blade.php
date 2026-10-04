<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 font-display text-sm font-semibold text-white hover:bg-primary-strong focus:outline-none focus:ring-4 focus:ring-primary/25 transition disabled:opacity-50']) }}>
    {{ $slot }}
</button>
