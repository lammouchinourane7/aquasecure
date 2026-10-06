@extends('layouts.back')

@section('title', $projet->titre)

@section('content')
    <a href="{{ route('admin.projets.index') }}" class="text-sm text-muted hover:text-ink">&larr; Projets de rénovation</a>

    <div class="flex flex-wrap items-start justify-between gap-4 mt-3 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-display text-2xl font-bold text-ink">{{ $projet->titre }}</h1>
                <x-badge :color="$projet->statut_badge_color">{{ $projet->statut_label }}</x-badge>
            </div>
            <p class="text-sm text-muted mt-1">
                Réseau :
                <a href="{{ route('admin.reseaux.show', $projet->reseau_id) }}" class="font-medium text-ink hover:text-primary underline">
                    {{ $projet->reseauEau?->type }}
                </a>
                &bull; Zone : <span class="font-medium text-ink">{{ $projet->reseauEau?->zone?->nom }} ({{ $projet->reseauEau?->zone?->commune }})</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-button href="{{ route('admin.financements.create', ['projet_id' => $projet->id]) }}" variant="primary">
                <x-icon.plus class="w-4 h-4" /> Allouer un financement
            </x-button>
            <x-button href="{{ route('admin.projets.edit', $projet) }}" variant="secondary">
                <x-icon.pencil class="w-4 h-4" /> Modifier
            </x-button>
            <form action="{{ route('admin.projets.destroy', $projet) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet et tous ses financements ?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">
                    <x-icon.trash class="w-4 h-4" />
                </x-button>
            </form>
        </div>
    </div>

    {{-- Cartes de synthèse --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white border border-border rounded-xl p-5">
            <span class="text-xs font-medium text-muted uppercase tracking-wider">Budget total mobilisé</span>
            <p class="font-display text-2xl font-bold text-primary mt-2">
                {{ number_format($totalMontant, 2, ',', ' ') }} <span class="text-base font-normal text-muted">TND</span>
            </p>
        </div>
        <div class="bg-white border border-border rounded-xl p-5">
            <span class="text-xs font-medium text-muted uppercase tracking-wider">Contributions</span>
            <p class="font-display text-2xl font-bold text-ink mt-2">
                {{ $projet->financements->count() }}
            </p>
        </div>
        <div class="bg-white border border-border rounded-xl p-5">
            <span class="text-xs font-medium text-muted uppercase tracking-wider">Statut des travaux</span>
            <div class="mt-2">
                <x-badge :color="$projet->statut_badge_color">{{ $projet->statut_label }}</x-badge>
            </div>
        </div>
        <div class="bg-white border border-border rounded-xl p-5">
            <span class="text-xs font-medium text-muted uppercase tracking-wider">État du réseau</span>
            <div class="mt-2">
                @php
                    $etatColors = ['bon' => 'success', 'degrade' => 'warning', 'hors_service' => 'alert'];
                @endphp
                <x-badge :color="$etatColors[$projet->reseauEau?->etat] ?? 'neutral'">
                    {{ ucfirst(str_replace('_', ' ', $projet->reseauEau?->etat ?? 'Inconnu')) }}
                </x-badge>
            </div>
        </div>
    </div>

    {{-- Répartition par source de financement --}}
    @if ($repartitionParSource->isNotEmpty())
        <div class="bg-white border border-border rounded-xl p-6 mb-8">
            <h2 class="font-display text-base font-bold text-ink mb-4">Répartition des sources de financement</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($repartitionParSource as $rep)
                    <div class="border border-border/80 rounded-lg p-4 bg-bg/30">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-sm text-ink">{{ $rep['label'] }}</span>
                            <span class="text-xs font-medium text-primary bg-primary/10 px-2 py-0.5 rounded-full">{{ $rep['pourcentage'] }} %</span>
                        </div>
                        <p class="font-display text-lg font-bold text-ink mt-2">
                            {{ number_format($rep['total'], 2, ',', ' ') }} TND
                        </p>
                        <p class="text-xs text-muted mt-1">{{ $rep['count'] }} apport{{ $rep['count'] > 1 ? 's' : '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tableau des financements alloués --}}
    <div class="bg-white border border-border rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <div>
                <h2 class="font-display text-base font-bold text-ink">Financements alloués au projet</h2>
                <p class="text-xs text-muted">Détail des contributions financières pour ce chantier de rénovation</p>
            </div>
            <x-button href="{{ route('admin.financements.create', ['projet_id' => $projet->id]) }}" variant="secondary" class="!py-1.5 !text-xs">
                <x-icon.plus class="w-3.5 h-3.5" /> Ajouter une source
            </x-button>
        </div>

        @if ($projet->financements->isEmpty())
            <div class="p-8 text-center">
                <x-icon.coin class="w-10 h-10 text-muted mx-auto mb-2 opacity-50" />
                <p class="text-sm font-medium text-ink">Aucun financement alloué pour le moment</p>
                <p class="text-xs text-muted mt-1">Ajoutez les aides de l'État, des municipalités, bailleurs ou dons.</p>
                <div class="mt-4">
                    <x-button href="{{ route('admin.financements.create', ['projet_id' => $projet->id]) }}" variant="primary">
                        Ajouter le premier financement
                    </x-button>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted bg-bg/50">
                            <th class="px-6 py-3 font-medium">Source</th>
                            <th class="px-6 py-3 font-medium">Montant</th>
                            <th class="px-6 py-3 font-medium">Date d'enregistrement</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($projet->financements as $financement)
                            <tr class="hover:bg-bg/40 transition">
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 font-medium text-ink">
                                        <x-icon.coin class="w-4 h-4 text-primary" />
                                        {{ $financement->source_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-display font-bold text-ink">
                                    {{ number_format($financement->montant, 2, ',', ' ') }} TND
                                </td>
                                <td class="px-6 py-3.5 text-muted text-xs">
                                    {{ $financement->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
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
        @endif
    </div>
@endsection
