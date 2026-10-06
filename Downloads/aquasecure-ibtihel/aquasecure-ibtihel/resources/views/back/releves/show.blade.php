@extends('layouts.back')

@section('title', 'Détail du relevé')

@section('content')
    <a href="{{ route('admin.releves.index') }}" class="text-sm text-muted hover:text-ink">&larr; Relevés</a>

    <div class="flex flex-wrap items-start justify-between gap-4 mt-3 mb-8">
        <div>
            <h1 class="font-display text-xl font-bold text-ink">
                {{ $releve->valeur }} {{ $releve->capteur?->unite }}
            </h1>
            <p class="text-sm text-muted mt-1">Relevé du {{ $releve->date_releve->format('d/m/Y à H:i') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <x-badge :color="$releve->hors_seuil ? 'alert' : 'success'">{{ $releve->hors_seuil ? 'Hors seuil' : 'Normal' }}</x-badge>
            <x-button href="{{ route('admin.releves.edit', $releve) }}" variant="secondary">Modifier</x-button>
            <form action="{{ route('admin.releves.destroy', $releve) }}" method="POST" onsubmit="return confirm('Supprimer ce relevé ?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">Supprimer</x-button>
            </form>
        </div>
    </div>

    <div class="bg-white border border-border rounded-xl p-6 max-w-xl">
        <dl class="space-y-4 text-sm">
            <div class="flex justify-between gap-4">
                <dt class="text-muted">Capteur</dt>
                <dd><a href="{{ route('admin.capteurs.show', $releve->capteur_id) }}" class="font-medium text-primary hover:underline">{{ $releve->capteur?->code }}</a></dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-muted">Type de mesure</dt>
                <dd class="font-medium text-ink">{{ $releve->capteur?->type_label }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-muted">Plage normale</dt>
                <dd class="font-medium text-ink">{{ $releve->capteur?->seuil_min }} – {{ $releve->capteur?->seuil_max }} {{ $releve->capteur?->unite }}</dd>
            </div>
            @if ($releve->hors_seuil && $releve->capteur)
                @php
                    $ecart = $releve->valeur > $releve->capteur->seuil_max
                        ? $releve->valeur - $releve->capteur->seuil_max
                        : $releve->capteur->seuil_min - $releve->valeur;
                @endphp
                <div class="flex justify-between gap-4">
                    <dt class="text-muted">Écart au seuil</dt>
                    <dd class="font-semibold text-alert-strong">{{ $releve->valeur > $releve->capteur->seuil_max ? '+' : '−' }}{{ round($ecart, 2) }} {{ $releve->capteur->unite }}</dd>
                </div>
            @endif
            <div class="flex justify-between gap-4">
                <dt class="text-muted">Réseau</dt>
                <dd class="font-medium text-ink text-right">{{ $releve->capteur?->reseauEau?->type ?? '—' }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-muted">Zone</dt>
                <dd class="font-medium text-ink">{{ $releve->capteur?->reseauEau?->zone?->nom ?? '—' }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-muted">Remarque</dt>
                <dd class="font-medium text-ink text-right">{{ $releve->remarque ?? '—' }}</dd>
            </div>
        </dl>
    </div>
@endsection
