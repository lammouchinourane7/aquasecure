@extends('layouts.back')

@section('title', 'Financements')

@use('App\Models\Financement')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display text-xl font-bold text-ink">Financements des rénovations</h1>
            <p class="text-sm text-muted mt-1">Contributions financières et subventions allouées aux chantiers d'eau potable</p>
        </div>
        <x-button href="{{ route('admin.financements.create') }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Ajouter un financement
        </x-button>
    </div>

    {{-- Cartes de synthèse --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-border rounded-xl p-4">
            <span class="text-xs font-medium text-muted uppercase tracking-wider">Montant total global</span>
            <p class="font-display text-2xl font-bold text-primary mt-1">
                {{ number_format($stats['montant_global'], 2, ',', ' ') }} <span class="text-xs font-normal text-muted">TND</span>
            </p>
        </div>
        <div class="bg-white border border-border rounded-xl p-4">
            <span class="text-xs font-medium text-muted uppercase tracking-wider">Total contributions</span>
            <p class="font-display text-2xl font-bold text-ink mt-1">{{ $stats['total_financements'] }}</p>
        </div>
        <div class="bg-white border border-border rounded-xl p-4">
            <span class="text-xs font-medium text-success-strong uppercase tracking-wider">Subventions État</span>
            <p class="font-display text-2xl font-bold text-ink mt-1">
                {{ number_format($stats['par_source']['Etat']->total ?? 0, 2, ',', ' ') }} <span class="text-xs font-normal text-muted">TND</span>
            </p>
        </div>
        <div class="bg-white border border-border rounded-xl p-4">
            <span class="text-xs font-medium text-warning-strong uppercase tracking-wider">Municipalités</span>
            <p class="font-display text-2xl font-bold text-ink mt-1">
                {{ number_format($stats['par_source']['municipalite']->total ?? 0, 2, ',', ' ') }} <span class="text-xs font-normal text-muted">TND</span>
            </p>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <x-input-label for="source" value="Source de financement" class="!text-xs !text-muted" />
            <x-select id="source" name="source" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Toutes les sources</option>
                @foreach (Financement::SOURCES as $key => $label)
                    <option value="{{ $key }}" @selected(request('source') === $key)>{{ $label }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex-1 min-w-[220px]">
            <x-input-label for="projet_id" value="Projet de rénovation" class="!text-xs !text-muted" />
            <x-select id="projet_id" name="projet_id" class="!py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous les projets</option>
                @foreach ($projets as $p)
                    <option value="{{ $p->id }}" @selected(request('projet_id') == $p->id)>
                        {{ $p->titre }}
                    </option>
                @endforeach
            </x-select>
        </div>
        <x-button type="submit" variant="secondary" class="!py-2">Filtrer</x-button>
        @if (request()->collect()->except('page')->filter()->isNotEmpty())
            <a href="{{ route('admin.financements.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white border border-border rounded-xl overflow-hidden">
        @if ($financements->isEmpty())
            <x-empty-state icon="coin" title="Aucun financement trouvé" description="Aucune source de financement ne correspond à ces critères.">
                <x-slot:action>
                    <x-button href="{{ route('admin.financements.create') }}" variant="primary">Ajouter un financement</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted bg-bg/50">
                            <th class="px-6 py-3 font-medium">Projet de rénovation</th>
                            <th class="px-6 py-3 font-medium">Réseau d'eau</th>
                            <th class="px-6 py-3 font-medium">Source</th>
                            <th class="px-6 py-3 font-medium">Montant</th>
                            <th class="px-6 py-3 font-medium">Date d'enregistrement</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($financements as $financement)
                            <tr class="hover:bg-bg/40 transition">
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('admin.projets.show', $financement->projet_id) }}" class="font-medium text-ink hover:text-primary">
                                        {{ $financement->projetRenovation?->titre }}
                                    </a>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="text-ink">{{ $financement->projetRenovation?->reseauEau?->type }}</span>
                                    <p class="text-xs text-muted">{{ $financement->projetRenovation?->reseauEau?->zone?->nom }} ({{ $financement->projetRenovation?->reseauEau?->zone?->commune }})</p>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-primary/10 text-primary-strong">
                                        <x-icon.coin class="w-3.5 h-3.5" />
                                        {{ $financement->source_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-display font-bold text-ink whitespace-nowrap">
                                    {{ number_format($financement->montant, 2, ',', ' ') }} TND
                                </td>
                                <td class="px-6 py-3.5 text-muted text-xs whitespace-nowrap">
                                    {{ $financement->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.financements.show', $financement) }}" class="text-muted hover:text-primary" title="Consulter">
                                            <x-icon.eye class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('admin.financements.edit', $financement) }}" class="text-muted hover:text-primary" title="Modifier">
                                            <x-icon.pencil class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('admin.financements.destroy', $financement) }}" method="POST" onsubmit="return confirm('Supprimer ce financement ?');">
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

            @if (request()->filled('source') || request()->filled('projet_id'))
                <div class="px-6 py-3 bg-bg/50 border-t border-border flex items-center justify-between text-sm">
                    <span class="text-muted">Total pour la sélection :</span>
                    <span class="font-display font-bold text-primary">{{ number_format($totalMontant, 2, ',', ' ') }} TND</span>
                </div>
            @endif
        @endif
    </div>

    @if ($financements->hasPages())
        <div class="mt-4">{{ $financements->links() }}</div>
    @endif
@endsection
