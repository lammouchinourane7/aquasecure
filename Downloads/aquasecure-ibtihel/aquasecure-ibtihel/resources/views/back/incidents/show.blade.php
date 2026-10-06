@extends('layouts.back')

@section('title', 'Incident #' . $incident->id)

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
                <h1 class="font-display text-2xl font-bold text-ink">Incident #{{ $incident->id }}</h1>
                <x-badge :color="$graviteBadge[$incident->gravite] ?? 'neutral'">
                    {{ $graviteLabel[$incident->gravite] ?? ucfirst($incident->gravite) }}
                </x-badge>
            </div>
            <p class="text-sm text-muted mt-1">Date de signalement : {{ $incident->date_signalement?->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.incidents.edit', $incident) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-sm font-medium text-ink hover:bg-bg transition">
                <x-icon.pencil class="w-4 h-4 text-muted" /> Modifier
            </a>
            <x-button href="{{ route('admin.interventions.create', ['incident_id' => $incident->id]) }}" variant="primary">
                <x-icon.plus class="w-4 h-4" /> Ajouter une intervention
            </x-button>
        </div>
    </div>

    <!-- Informations Générales -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-border rounded-xl p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Type d'incident</p>
            <p class="font-display text-lg font-bold text-ink">{{ $typeLabel[$incident->type] ?? ucfirst($incident->type) }}</p>
        </div>

        <div class="bg-white border border-border rounded-xl p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Réseau d'eau</p>
            @if ($incident->reseauEau)
                <a href="{{ route('admin.reseaux.show', $incident->reseauEau) }}" class="font-display text-lg font-bold text-primary hover:underline">
                    {{ ucfirst($incident->reseauEau->type) }}
                </a>
                <p class="text-xs text-muted mt-0.5">Zone : {{ $incident->reseauEau->zone?->nom ?? '—' }} ({{ $incident->reseauEau->zone?->commune ?? '—' }})</p>
            @else
                <p class="text-lg font-medium text-muted">—</p>
            @endif
        </div>

        <div class="bg-white border border-border rounded-xl p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Total Interventions</p>
            <p class="font-display text-lg font-bold text-ink">{{ $incident->interventions->count() }}</p>
        </div>
    </div>

    <!-- Liste des interventions associées -->
    <div class="bg-white border border-border rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h2 class="font-display text-base font-bold text-ink">Interventions associées</h2>
            <a href="{{ route('admin.interventions.create', ['incident_id' => $incident->id]) }}" class="text-sm font-medium text-primary hover:underline">
                + Nouvelle intervention
            </a>
        </div>

        @if ($incident->interventions->isEmpty())
            <div class="p-8 text-center text-muted text-sm">
                Aucune intervention n'a encore été enregistrée pour cet incident.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted bg-bg/50">
                            <th class="px-6 py-3 font-medium"># ID</th>
                            <th class="px-6 py-3 font-medium">Date d'intervention</th>
                            <th class="px-6 py-3 font-medium">Statut</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incident->interventions as $intervention)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3.5 font-medium text-ink">
                                    <a href="{{ route('admin.interventions.show', $intervention) }}" class="hover:text-primary">
                                        #{{ $intervention->id }}
                                    </a>
                                </td>
                                <td class="px-6 py-3.5 text-ink/80 whitespace-nowrap">
                                    {{ $intervention->date_intervention?->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <x-badge :color="$statutBadge[$intervention->statut] ?? 'neutral'">
                                        {{ $statutLabel[$intervention->statut] ?? ucfirst($intervention->statut) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.interventions.show', $intervention) }}" class="text-muted hover:text-primary" title="Voir"><x-icon.eye class="w-4 h-4" /></a>
                                        <a href="{{ route('admin.interventions.edit', $intervention) }}" class="text-muted hover:text-primary" title="Modifier"><x-icon.pencil class="w-4 h-4" /></a>
                                        <form action="{{ route('admin.interventions.destroy', $intervention) }}" method="POST" onsubmit="return confirm('Supprimer cette intervention ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-muted hover:text-alert-strong" title="Supprimer"><x-icon.trash class="w-4 h-4" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.incidents.index') }}" class="text-sm font-medium text-muted hover:text-ink">
            &larr; Retour à la liste des incidents
        </a>
    </div>
@endsection
