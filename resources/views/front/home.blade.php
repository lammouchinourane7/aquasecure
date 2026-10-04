@extends('layouts.front')

@section('title', 'Accueil')

@section('content')
    <section class="max-w-6xl mx-auto px-6 pt-12 pb-10">
        <a href="{{ route('reseaux.index') }}" class="inline-block group">
            <span class="font-display text-5xl sm:text-6xl font-bold text-ink group-hover:text-primary transition">{{ $reseauxCount }}</span>
            <p class="text-muted mt-1 group-hover:text-ink transition">réseaux surveillés &rarr;</p>
        </a>
    </section>

    <section class="max-w-6xl mx-auto px-6 pb-16">
        <div class="rounded-xl border border-border bg-white p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-lg font-bold text-ink">Carte des réseaux</h2>
                <a href="{{ route('reseaux.index') }}" class="text-sm font-medium text-primary hover:underline">Voir la liste</a>
            </div>
            <div class="rounded-lg bg-bg border border-border border-dashed h-72 flex flex-col items-center justify-center text-center">
                <x-icon.map class="w-8 h-8 text-muted mb-2" />
                <p class="text-sm text-muted">Carte interactive des réseaux par zone — à connecter prochainement.</p>
            </div>
        </div>
    </section>
@endsection
