@extends('layouts.back')

@section('title', 'Intervention #' . $intervention->id)

@php
    $typeLabel = ['fuite' => 'Fuite', 'contamination' => 'Contamination', 'coupure' => 'Coupure'];
    $graviteBadge = ['faible' => 'neutral', 'moyenne' => 'warning', 'critique' => 'alert'];
    $graviteLabel = ['faible' => 'Faible', 'moyenne' => 'Moyenne', 'critique' => 'Critique'];
    $statutBadge = ['planifiee' => 'neutral', 'en_cours' => 'warning', 'terminee' => 'success'];
    $statutLabel = ['planifiee' => 'Planifiée', 'en_cours' => 'En cours', 'terminee' => 'Terminée'];
@endphp

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-display text-2xl font-bold text-ink">Intervention #{{ $intervention->id }}</h1>
                <x-badge :color="$statutBadge[$intervention->statut] ?? 'neutral'">
                    {{ $statutLabel[$intervention->statut] ?? ucfirst($intervention->statut) }}
                </x-badge>
            </div>
            <p class="text-sm text-muted mt-1">Date d'exécution : {{ $intervention->date_intervention?->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.interventions.edit', $intervention) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-sm font-medium text-ink hover:bg-bg transition">
                <x-icon.pencil class="w-4 h-4 text-muted" /> Modifier
            </a>
            <form action="{{ route('admin.interventions.destroy', $intervention) }}" method="POST" onsubmit="return confirm('Supprimer cette intervention ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-alert/30 text-sm font-medium text-alert-strong hover:bg-alert/10 transition">
                    <x-icon.trash class="w-4 h-4" /> Supprimer
                </button>
            </form>
        </div>
    </div>

    <!-- Details Card -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white border border-border rounded-xl p-6">
            <h2 class="font-display text-base font-bold text-ink mb-4 pb-2 border-b border-border">Incident associé</h2>
            @if ($intervention->incident)
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-muted block text-xs">Identifiant</span>
                        <a href="{{ route('admin.incidents.show', $intervention->incident) }}" class="font-semibold text-primary hover:underline">
                            Incident #{{ $intervention->incident->id }}
                        </a>
                    </div>
                    <div>
                        <span class="text-muted block text-xs">Type d'incident</span>
                        <span class="font-medium text-ink">{{ $typeLabel[$intervention->incident->type] ?? ucfirst($intervention->incident->type) }}</span>
                    </div>
                    <div>
                        <span class="text-muted block text-xs">Gravité</span>
                        <x-badge :color="$graviteBadge[$intervention->incident->gravite] ?? 'neutral'">
                            {{ $graviteLabel[$intervention->incident->gravite] ?? ucfirst($intervention->incident->gravite) }}
                        </x-badge>
                    </div>
                    <div>
                        <span class="text-muted block text-xs">Date de signalement</span>
                        <span class="text-ink/80">{{ $intervention->incident->date_signalement?->format('d/m/Y') }}</span>
                    </div>
                </div>
            @else
                <p class="text-muted text-sm">— Aucun incident rattaché</p>
            @endif
        </div>

        <div class="bg-white border border-border rounded-xl p-6">
            <h2 class="font-display text-base font-bold text-ink mb-4 pb-2 border-b border-border">Réseau d'eau & Localisation</h2>
            @if ($intervention->incident?->reseauEau)
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-muted block text-xs">Type de réseau</span>
                        <a href="{{ route('admin.reseaux.show', $intervention->incident->reseauEau) }}" class="font-semibold text-primary hover:underline">
                            {{ ucfirst($intervention->incident->reseauEau->type) }}
                        </a>
                    </div>
                    <div>
                        <span class="text-muted block text-xs">Zone géographique</span>
                        <span class="font-medium text-ink">{{ $intervention->incident->reseauEau->zone?->nom ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-muted block text-xs">Commune</span>
                        <span class="text-ink/80">{{ $intervention->incident->reseauEau->zone?->commune ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-muted block text-xs">État du réseau</span>
                        <span class="text-ink/80">{{ ucfirst($intervention->incident->reseauEau->etat) }}</span>
                    </div>
                </div>
            @else
                <p class="text-muted text-sm">—</p>
            @endif
        </div>
    </div>

    <div>
        <a href="{{ route('admin.interventions.index') }}" class="text-sm font-medium text-muted hover:text-ink">
            &larr; Retour à la liste des interventions
        </a>
    </div>
@endsection
