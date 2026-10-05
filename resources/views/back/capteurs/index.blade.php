@extends('layouts.back')

@section('title', 'Capteurs')

@php
    $statutBadge = ['actif' => 'success', 'en_panne' => 'warning', 'hors_service' => 'alert'];
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display text-xl font-bold text-ink">Capteurs</h1>
        <x-button href="{{ route('admin.capteurs.create') }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Ajouter
        </x-button>
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative w-full sm:w-64">
            <x-icon.search class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2" />
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Code ou modèle..."
                   class="w-full rounded-lg border border-border bg-white pl-9 pr-4 py-2 text-sm text-ink focus:border-primary focus:ring-4 focus:ring-primary/15 transition">
        </div>
        <x-select name="type_mesure" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
            <option value="">Tous les types</option>
            @foreach (\App\Models\Capteur::TYPES as $key => $t)
                <option value="{{ $key }}" @selected(request('type_mesure') === $key)>{{ $t['label'] }}</option>
            @endforeach
        </x-select>
        <x-select name="statut" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach (\App\Models\Capteur::STATUTS as $key => $label)
                <option value="{{ $key }}" @selected(request('statut') === $key)>{{ $label }}</option>
            @endforeach
        </x-select>
        <x-select name="reseau_id" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
            <option value="">Tous les réseaux</option>
            @foreach ($reseaux as $reseau)
                <option value="{{ $reseau->id }}" @selected(request('reseau_id') == $reseau->id)>{{ $reseau->type }}</option>
            @endforeach
        </x-select>
        @if (request()->hasAny(['q', 'type_mesure', 'statut', 'reseau_id']) && request()->collect()->filter()->isNotEmpty())
            <a href="{{ route('admin.capteurs.index') }}" class="text-sm font-medium text-muted hover:text-ink">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white border border-border">
        @if ($capteurs->isEmpty())
            <x-empty-state icon="radar" title="Aucun capteur" description="Aucun capteur ne correspond à ces critères.">
                <x-slot:action>
                    <x-button href="{{ route('admin.capteurs.create') }}" variant="primary">Ajouter un capteur</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted">
                            <th class="px-6 py-3 font-medium">Code</th>
                            <th class="px-6 py-3 font-medium">Mesure</th>
                            <th class="px-6 py-3 font-medium">Réseau</th>
                            <th class="px-6 py-3 font-medium">Dernier relevé</th>
                            <th class="px-6 py-3 font-medium">Relevés</th>
                            <th class="px-6 py-3 font-medium">Statut</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($capteurs as $capteur)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('admin.capteurs.show', $capteur) }}" class="font-medium text-ink hover:text-primary">{{ $capteur->code }}</a>
                                    <p class="text-xs text-muted">{{ $capteur->modele }}</p>
                                </td>
                                <td class="px-6 py-3.5 text-ink/80 whitespace-nowrap">
                                    {{ $capteur->type_label }}
                                    <p class="text-xs text-muted">{{ $capteur->seuil_min }} – {{ $capteur->seuil_max }} {{ $capteur->unite }}</p>
                                </td>
                                <td class="px-6 py-3.5 text-ink/80 min-w-[12rem]">
                                    {{ $capteur->reseauEau?->type ?? '—' }}
                                    <p class="text-xs text-muted">{{ $capteur->reseauEau?->zone?->nom }}</p>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if ($capteur->dernierReleve)
                                        <span class="{{ $capteur->dernierReleve->hors_seuil ? 'text-alert-strong font-semibold' : 'text-ink' }}">
                                            {{ $capteur->dernierReleve->valeur }} {{ $capteur->unite }}
                                        </span>
                                        <p class="text-xs text-muted">{{ $capteur->dernierReleve->date_releve->locale('fr')->diffForHumans() }}</p>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-ink/80 whitespace-nowrap">
                                    {{ $capteur->releves_count }}
                                    @if ($capteur->anomalies_count > 0)
                                        <p class="text-xs font-semibold text-alert-strong">{{ $capteur->anomalies_count }} hors seuil</p>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <x-badge :color="$statutBadge[$capteur->statut] ?? 'neutral'">{{ $capteur->statut_label }}</x-badge>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.capteurs.show', $capteur) }}" class="text-muted hover:text-primary" title="Voir">
                                            <x-icon.eye class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.capteurs.edit', $capteur) }}" class="text-muted hover:text-primary" title="Modifier">
                                            <x-icon.pencil class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('admin.capteurs.destroy', $capteur) }}" method="POST"
                                              onsubmit="return confirm('Supprimer le capteur {{ $capteur->code }} ? Ses {{ $capteur->releves_count }} relevé(s) seront aussi supprimés.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-muted hover:text-alert-strong" title="Supprimer">
                                                <x-icon.trash class="w-4 h-4" />
                                            </button>
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

    @if ($capteurs->hasPages())
        <div class="mt-4">{{ $capteurs->links() }}</div>
    @endif
@endsection
