@extends('layouts.back')

@section('title', 'Projets de rénovation')

@use('App\Models\ProjetRenovation')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display text-xl font-bold text-ink">Projets de rénovation</h1>
            <p class="text-sm text-muted mt-1">Planification des travaux et suivi du financement des infrastructures d'eau</p>
        </div>
        <x-button href="{{ route('admin.projets.create') }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Nouveau projet
        </x-button>
    </div>

    {{-- Cartes de synthèse --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-border rounded-xl p-4">
            <span class="text-xs font-medium text-muted uppercase tracking-wider">Total projets</span>
            <p class="font-display text-2xl font-bold text-ink mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-border rounded-xl p-4">
            <span class="text-xs font-medium text-warning-strong uppercase tracking-wider">Planifiés</span>
            <p class="font-display text-2xl font-bold text-ink mt-1">{{ $stats['planifie'] }}</p>
        </div>
        <div class="bg-white border border-border rounded-xl p-4">
            <span class="text-xs font-medium text-primary uppercase tracking-wider">En cours</span>
            <p class="font-display text-2xl font-bold text-ink mt-1">{{ $stats['en_cours'] }}</p>
        </div>
        <div class="bg-white border border-border rounded-xl p-4">
            <span class="text-xs font-medium text-success-strong uppercase tracking-wider">Terminés</span>
            <p class="font-display text-2xl font-bold text-ink mt-1">{{ $stats['termine'] }}</p>
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex-1 min-w-[200px]">
            <x-input-label for="q" value="Recherche" class="!text-xs !text-muted" />
            <x-text-input id="q" name="q" :value="request('q')" placeholder="Rechercher par titre..." class="!py-2" />
        </div>
        <div>
            <x-input-label for="statut" value="Statut" class="!text-xs !text-muted" />
            <x-select id="statut" name="statut" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                @foreach (ProjetRenovation::STATUTS as $key => $label)
                    <option value="{{ $key }}" @selected(request('statut') === $key)>{{ $label }}</option>
                @endforeach
            </x-select>
        </div>
        <div>
            <x-input-label for="reseau_id" value="Réseau" class="!text-xs !text-muted" />
            <x-select id="reseau_id" name="reseau_id" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous les réseaux</option>
                @foreach ($reseaux as $reseau)
                    <option value="{{ $reseau->id }}" @selected(request('reseau_id') == $reseau->id)>
                        {{ $reseau->type }} ({{ $reseau->zone?->nom }})
                    </option>
                @endforeach
            </x-select>
        </div>
        <x-button type="submit" variant="secondary" class="!py-2">Filtrer</x-button>
        @if (request()->collect()->except('page')->filter()->isNotEmpty())
            <a href="{{ route('admin.projets.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white border border-border rounded-xl overflow-hidden">
        @if ($projets->isEmpty())
            <x-empty-state icon="building" title="Aucun projet trouvé" description="Aucun projet de rénovation ne correspond à vos critères de recherche.">
                <x-slot:action>
                    <x-button href="{{ route('admin.projets.create') }}" variant="primary">Ajouter un projet</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted bg-bg/50">
                            <th class="px-6 py-3 font-medium">Titre du projet</th>
                            <th class="px-6 py-3 font-medium">Réseau associé</th>
                            <th class="px-6 py-3 font-medium">Statut</th>
                            <th class="px-6 py-3 font-medium">Financements</th>
                            <th class="px-6 py-3 font-medium">Budget alloué</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($projets as $projet)
                            <tr class="hover:bg-bg/40 transition">
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('admin.projets.show', $projet) }}" class="font-medium text-ink hover:text-primary">
                                        {{ $projet->titre }}
                                    </a>
                                </td>
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('admin.reseaux.show', $projet->reseau_id) }}" class="text-ink hover:text-primary">
                                        {{ $projet->reseauEau?->type }}
                                    </a>
                                    <p class="text-xs text-muted">{{ $projet->reseauEau?->zone?->nom }} ({{ $projet->reseauEau?->zone?->commune }})</p>
                                </td>
                                <td class="px-6 py-3.5">
                                    <x-badge :color="$projet->statut_badge_color">
                                        {{ $projet->statut_label }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-3.5 text-ink/80">
                                    <span class="inline-flex items-center gap-1 font-medium">
                                        <x-icon.coin class="w-4 h-4 text-muted" />
                                        {{ $projet->financements_count }} contribution{{ $projet->financements_count > 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-display font-semibold text-ink whitespace-nowrap">
                                    {{ number_format($projet->financements_sum_montant ?? 0, 2, ',', ' ') }} TND
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.projets.show', $projet) }}" class="text-muted hover:text-primary" title="Consulter la fiche">
                                            <x-icon.eye class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.projets.edit', $projet) }}" class="text-muted hover:text-primary" title="Modifier le projet">
                                            <x-icon.pencil class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('admin.projets.destroy', $projet) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet et tous ses financements ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-muted hover:text-alert-strong" title="Supprimer le projet">
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

    @if ($projets->hasPages())
        <div class="mt-4">{{ $projets->links() }}</div>
    @endif
@endsection
