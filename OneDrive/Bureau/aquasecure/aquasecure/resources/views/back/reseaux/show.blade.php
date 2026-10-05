@extends('layouts.back')

@section('title', 'Fiche réseau')

@php
    $etatBadge = ['bon' => 'success', 'degrade' => 'warning', 'hors_service' => 'alert'];
    $etatLabel = ['bon' => 'Bon', 'degrade' => 'Dégradé', 'hors_service' => 'Hors service'];
@endphp

@section('content')
    <a href="{{ route('admin.reseaux.index') }}" class="text-sm text-muted hover:text-ink">&larr; Réseaux</a>

    <div class="flex items-start justify-between mt-3 mb-8">
        <div>
            <h1 class="font-display text-xl font-bold text-ink">{{ $reseau->type }}</h1>
            <p class="text-sm text-muted mt-1">{{ $reseau->zone?->nom ?? 'Zone inconnue' }} ({{ $reseau->zone?->commune }})</p>
        </div>
        <div class="flex items-center gap-3">
            <x-badge :color="$etatBadge[$reseau->etat] ?? 'neutral'">{{ $etatLabel[$reseau->etat] ?? ucfirst($reseau->etat) }}</x-badge>
            <x-button href="{{ route('admin.reseaux.edit', $reseau) }}" variant="secondary">Modifier</x-button>
        </div>
    </div>

    <div class="bg-white border border-border rounded-xl p-6 max-w-xl">
        <dl class="space-y-4 text-sm">
            <div class="flex justify-between">
                <dt class="text-muted">Type</dt>
                <dd class="font-medium text-ink">{{ $reseau->type }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-muted">Zone</dt>
                <dd class="font-medium text-ink">{{ $reseau->zone?->nom ?? '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-muted">Commune</dt>
                <dd class="font-medium text-ink">{{ $reseau->zone?->commune ?? '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-muted">État</dt>
                <dd><x-badge :color="$etatBadge[$reseau->etat] ?? 'neutral'">{{ $etatLabel[$reseau->etat] ?? ucfirst($reseau->etat) }}</x-badge></dd>
            </div>
        </dl>
    </div>
@endsection
