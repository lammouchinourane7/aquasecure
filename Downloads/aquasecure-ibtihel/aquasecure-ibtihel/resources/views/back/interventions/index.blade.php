@extends('layouts.back')

@section('title', 'Interventions')

@php
    $typeLabel = ['fuite' => 'Fuite', 'contamination' => 'Contamination', 'coupure' => 'Coupure'];
    $statutBadge = ['planifiee' => 'neutral', 'en_cours' => 'warning', 'terminee' => 'success'];
    $statutLabel = ['planifiee' => 'Planifiée', 'en_cours' => 'En cours', 'terminee' => 'Terminée'];
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display text-xl font-bold text-ink">Interventions</h1>
        <x-button href="{{ route('admin.interventions.create', request()->only('incident_id')) }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Programmer une intervention
        </x-button>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <x-input-label for="incident_id" value="Incident" class="!text-xs !text-muted" />
            <x-select id="incident_id" name="incident_id" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous les incidents</option>
                @foreach ($incidents as $inc)
                    <option value="{{ $inc->id }}" @selected(request('incident_id') == $inc->id)>
                        Incident #{{ $inc->id }} ({{ $typeLabel[$inc->type] ?? ucfirst($inc->type) }})
                    </option>
                @endforeach
            </x-select>
        </div>
        <div>
            <x-input-label for="statut" value="Statut" class="!text-xs !text-muted" />
            <x-select id="statut" name="statut" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="planifiee" @selected(request('statut') === 'planifiee')>Planifiée</option>
                <option value="en_cours" @selected(request('statut') === 'en_cours')>En cours</option>
                <option value="terminee" @selected(request('statut') === 'terminee')>Terminée</option>
            </x-select>
        </div>
        <div>
            <x-input-label for="du" value="Du" class="!text-xs !text-muted" />
            <x-text-input id="du" name="du" type="date" :value="request('du')" class="!w-auto !py-2" onchange="this.form.submit()" />
        </div>
        <div>
            <x-input-label for="au" value="Au" class="!text-xs !text-muted" />
            <x-text-input id="au" name="au" type="date" :value="request('au')" class="!w-auto !py-2" onchange="this.form.submit()" />
        </div>
        @if (request()->collect()->except('page')->filter()->isNotEmpty())
            <a href="{{ route('admin.interventions.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white border border-border">
        @if ($interventions->isEmpty())
            <x-empty-state icon="wrench" title="Aucune intervention" description="Aucune intervention ne correspond à ces critères.">
                <x-slot:action>
                    <x-button href="{{ route('admin.interventions.create') }}" variant="primary">Programmer une intervention</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted">
                            <th class="px-6 py-3 font-medium"># ID</th>
                            <th class="px-6 py-3 font-medium">Incident</th>
                            <th class="px-6 py-3 font-medium">Réseau / Zone</th>
                            <th class="px-6 py-3 font-medium">Date d'intervention</th>
                            <th class="px-6 py-3 font-medium">Statut</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($interventions as $intervention)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3.5 font-medium text-ink whitespace-nowrap">
                                    <a href="{{ route('admin.interventions.show', $intervention) }}" class="hover:text-primary">#{{ $intervention->id }}</a>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-ink">
                                    @if ($intervention->incident)
                                        <a href="{{ route('admin.incidents.show', $intervention->incident) }}" class="hover:text-primary">
                                            Incident #{{ $intervention->incident->id }} ({{ $typeLabel[$intervention->incident->type] ?? ucfirst($intervention->incident->type) }})
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-ink/80">
                                    @if ($intervention->incident?->reseauEau)
                                        <a href="{{ route('admin.reseaux.show', $intervention->incident->reseauEau) }}" class="hover:text-primary">
                                            {{ ucfirst($intervention->incident->reseauEau->type) }} ({{ $intervention->incident->reseauEau->zone?->nom ?? '—' }})
                                        </a>
                                    @else
                                        —
                                    @endif
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

    @if ($interventions->hasPages())
        <div class="mt-4">{{ $interventions->links() }}</div>
    @endif
@endsection
