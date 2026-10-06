@extends('layouts.back')

@section('title', 'Détail du financement')

@section('content')
    <a href="{{ route('admin.financements.index') }}" class="text-sm text-muted hover:text-ink">&larr; Financements</a>

    <div class="flex items-start justify-between mt-3 mb-8">
        <div>
            <h1 class="font-display text-xl font-bold text-ink">Financement #{{ $financement->id }}</h1>
            <p class="text-sm text-muted mt-1">
                Projet :
                <a href="{{ route('admin.projets.show', $financement->projet_id) }}" class="font-medium text-primary hover:underline">
                    {{ $financement->projetRenovation?->titre }}
                </a>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-button href="{{ route('admin.financements.edit', $financement) }}" variant="secondary">
                <x-icon.pencil class="w-4 h-4" /> Modifier
            </x-button>
            <form action="{{ route('admin.financements.destroy', $financement) }}" method="POST" onsubmit="return confirm('Supprimer ce financement ?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">
                    <x-icon.trash class="w-4 h-4" />
                </x-button>
            </form>
        </div>
    </div>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-xl">
        <dl class="space-y-4 text-sm">
            <div class="flex justify-between py-2 border-b border-border/50">
                <dt class="text-muted">Source</dt>
                <dd class="font-medium text-ink flex items-center gap-1.5">
                    <x-icon.coin class="w-4 h-4 text-primary" />
                    {{ $financement->source_label }}
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b border-border/50">
                <dt class="text-muted">Montant alloué</dt>
                <dd class="font-display text-lg font-bold text-primary">
                    {{ number_format($financement->montant, 2, ',', ' ') }} TND
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b border-border/50">
                <dt class="text-muted">Projet financé</dt>
                <dd class="font-medium text-ink text-right">
                    <a href="{{ route('admin.projets.show', $financement->projet_id) }}" class="text-primary hover:underline">
                        {{ $financement->projetRenovation?->titre }}
                    </a>
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b border-border/50">
                <dt class="text-muted">Réseau concerné</dt>
                <dd class="font-medium text-ink text-right">
                    {{ $financement->projetRenovation?->reseauEau?->type }}
                    <span class="text-xs text-muted block">{{ $financement->projetRenovation?->reseauEau?->zone?->nom }}</span>
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b border-border/50">
                <dt class="text-muted">Date d'enregistrement</dt>
                <dd class="font-medium text-ink">{{ $financement->created_at?->format('d/m/Y à H:i') ?? '—' }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-muted">Dernière mise à jour</dt>
                <dd class="font-medium text-ink">{{ $financement->updated_at?->format('d/m/Y à H:i') ?? '—' }}</dd>
            </div>
        </dl>
    </div>
@endsection
