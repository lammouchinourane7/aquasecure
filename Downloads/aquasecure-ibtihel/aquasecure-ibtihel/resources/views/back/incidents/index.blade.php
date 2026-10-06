@extends('layouts.back')

@section('title', 'Incidents')

@php
    $typeLabel = ['fuite' => 'Fuite', 'contamination' => 'Contamination', 'coupure' => 'Coupure'];
    $graviteBadge = ['faible' => 'neutral', 'moyenne' => 'warning', 'critique' => 'alert'];
    $graviteLabel = ['faible' => 'Faible', 'moyenne' => 'Moyenne', 'critique' => 'Critique'];
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display text-xl font-bold text-ink">Incidents</h1>
        <x-button href="{{ route('admin.incidents.create', request()->only('reseau_id')) }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Déclarer un incident
        </x-button>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <x-input-label for="reseau_id" value="Réseau" class="!text-xs !text-muted" />
            <x-select id="reseau_id" name="reseau_id" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous les réseaux</option>
                @foreach ($reseaux as $reseau)
                    <option value="{{ $reseau->id }}" @selected(request('reseau_id') == $reseau->id)>
                        {{ ucfirst($reseau->type) }} — {{ $reseau->zone?->nom }}
                    </option>
                @endforeach
            </x-select>
        </div>
        <div>
            <x-input-label for="type" value="Type" class="!text-xs !text-muted" />
            <x-select id="type" name="type" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                <option value="fuite" @selected(request('type') === 'fuite')>Fuite</option>
                <option value="contamination" @selected(request('type') === 'contamination')>Contamination</option>
                <option value="coupure" @selected(request('type') === 'coupure')>Coupure</option>
            </x-select>
        </div>
        <div>
            <x-input-label for="gravite" value="Gravité" class="!text-xs !text-muted" />
            <x-select id="gravite" name="gravite" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Toutes gravités</option>
                <option value="faible" @selected(request('gravite') === 'faible')>Faible</option>
                <option value="moyenne" @selected(request('gravite') === 'moyenne')>Moyenne</option>
                <option value="critique" @selected(request('gravite') === 'critique')>Critique</option>
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
            <a href="{{ route('admin.incidents.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white border border-border">
        @if ($incidents->isEmpty())
            <x-empty-state icon="alert-triangle" title="Aucun incident" description="Aucun incident ne correspond à ces critères.">
                <x-slot:action>
                    <x-button href="{{ route('admin.incidents.create') }}" variant="primary">Déclarer un incident</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted">
                            <th class="px-6 py-3 font-medium"># ID</th>
                            <th class="px-6 py-3 font-medium">Type</th>
                            <th class="px-6 py-3 font-medium">Gravité</th>
                            <th class="px-6 py-3 font-medium">Réseau / Zone</th>
                            <th class="px-6 py-3 font-medium">Date signalement</th>
                            <th class="px-6 py-3 font-medium">Interventions</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incidents as $incident)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3.5 font-medium text-ink whitespace-nowrap">
                                    <a href="{{ route('admin.incidents.show', $incident) }}" class="hover:text-primary">#{{ $incident->id }}</a>
                                </td>
                                <td class="px-6 py-3.5 font-medium text-ink">
                                    {{ $typeLabel[$incident->type] ?? ucfirst($incident->type) }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <x-badge :color="$graviteBadge[$incident->gravite] ?? 'neutral'">
                                        {{ $graviteLabel[$incident->gravite] ?? ucfirst($incident->gravite) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-3.5 text-ink/80">
                                    @if ($incident->reseauEau)
                                        <a href="{{ route('admin.reseaux.show', $incident->reseauEau) }}" class="hover:text-primary">
                                            {{ ucfirst($incident->reseauEau->type) }} ({{ $incident->reseauEau->zone?->nom ?? '—' }})
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-ink/80 whitespace-nowrap">
                                    {{ $incident->date_signalement?->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-3.5 text-ink/80">
                                    <span class="inline-flex items-center gap-1">
                                        <x-icon.wrench class="w-4 h-4 text-muted" />
                                        {{ $incident->interventions_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.incidents.show', $incident) }}" class="text-muted hover:text-primary" title="Voir"><x-icon.eye class="w-4 h-4" /></a>
                                        <a href="{{ route('admin.incidents.edit', $incident) }}" class="text-muted hover:text-primary" title="Modifier"><x-icon.pencil class="w-4 h-4" /></a>
                                        <form action="{{ route('admin.incidents.destroy', $incident) }}" method="POST" onsubmit="return confirm('Supprimer cet incident et toutes ses interventions ?');">
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

    @if ($incidents->hasPages())
        <div class="mt-4">{{ $incidents->links() }}</div>
    @endif
@endsection
