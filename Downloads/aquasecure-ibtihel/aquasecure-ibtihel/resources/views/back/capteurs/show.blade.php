@extends('layouts.back')

@section('title', 'Fiche capteur')

@php
    $statutBadge = ['actif' => 'success', 'en_panne' => 'warning', 'hors_service' => 'alert'];

    // Préparation du graphique (SVG) des derniers relevés
    $W = 640; $H = 220; $pad = 28;
    $valeurs = $serie->pluck('valeur');
    $chart = null;
    if ($valeurs->count() >= 2) {
        $yMin = min($valeurs->min(), $capteur->seuil_min);
        $yMax = max($valeurs->max(), $capteur->seuil_max);
        $marge = ($yMax - $yMin) * 0.1 ?: 1;
        $yMin -= $marge; $yMax += $marge;
        $y = fn ($v) => round($H - $pad - (($v - $yMin) / ($yMax - $yMin)) * ($H - 2 * $pad), 1);
        $x = fn ($i) => round($pad + $i * (($W - 2 * $pad) / ($valeurs->count() - 1)), 1);
        $chart = [
            'points' => $valeurs->map(fn ($v, $i) => $x($i).','.$y($v))->implode(' '),
            'dots' => $serie->map(fn ($r, $i) => ['x' => $x($i), 'y' => $y($r->valeur), 'alerte' => $r->hors_seuil, 'titre' => $r->valeur.' '.$capteur->unite.' — '.$r->date_releve->format('d/m H:i')]),
            'bandTop' => $y($capteur->seuil_max),
            'bandBottom' => $y($capteur->seuil_min),
        ];
    }
@endphp

@section('content')
    <a href="{{ route('admin.capteurs.index') }}" class="text-sm text-muted hover:text-ink">&larr; Capteurs</a>

    <div class="flex flex-wrap items-start justify-between gap-4 mt-3 mb-8">
        <div>
            <h1 class="font-display text-xl font-bold text-ink">{{ $capteur->code }}</h1>
            <p class="text-sm text-muted mt-1">
                {{ $capteur->type_label }} · {{ $capteur->modele }} ·
                <a href="{{ route('admin.reseaux.show', $capteur->reseau_id) }}" class="hover:text-primary">{{ $capteur->reseauEau?->type }}</a>
                ({{ $capteur->reseauEau?->zone?->nom }})
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-badge :color="$statutBadge[$capteur->statut] ?? 'neutral'">{{ $capteur->statut_label }}</x-badge>
            @if ($capteur->statut !== 'hors_service')
                <x-button href="{{ route('admin.releves.create', ['capteur_id' => $capteur->id]) }}" variant="primary">
                    <x-icon.plus class="w-4 h-4" /> Nouveau relevé
                </x-button>
            @endif
            <x-button href="{{ route('admin.capteurs.edit', $capteur) }}" variant="secondary">Modifier</x-button>
        </div>
    </div>

    {{-- Statistiques calculées sur l'ensemble des relevés --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="border-l-4 border-primary pl-4">
            <span class="font-display text-3xl font-bold text-ink">{{ $stats->total }}</span>
            <p class="text-sm text-muted mt-1">Relevés</p>
        </div>
        <div class="border-l-4 border-success pl-4">
            <span class="font-display text-3xl font-bold text-ink">{{ $stats->total ? round($stats->moyenne, 2) : '—' }}</span>
            <p class="text-sm text-muted mt-1">Moyenne ({{ $capteur->unite }})</p>
        </div>
        <div class="border-l-4 border-border pl-4">
            <span class="font-display text-3xl font-bold text-ink">{{ $stats->total ? round($stats->minimum, 2) : '—' }}</span>
            <p class="text-sm text-muted mt-1">Minimum</p>
        </div>
        <div class="border-l-4 border-border pl-4">
            <span class="font-display text-3xl font-bold text-ink">{{ $stats->total ? round($stats->maximum, 2) : '—' }}</span>
            <p class="text-sm text-muted mt-1">Maximum</p>
        </div>
        <div class="border-l-4 border-alert pl-4">
            <span class="font-display text-3xl font-bold text-ink">{{ (int) $stats->anomalies }}</span>
            <p class="text-sm text-muted mt-1">
                Anomalies{{ $stats->total ? ' ('.round($stats->anomalies / $stats->total * 100).' %)' : '' }}
            </p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-border rounded-xl p-6">
            <h2 class="font-display text-base font-bold text-ink mb-4">Caractéristiques</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-muted">Type de mesure</dt><dd class="font-medium text-ink">{{ $capteur->type_label }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-muted">Unité</dt><dd class="font-medium text-ink">{{ $capteur->unite }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-muted">Plage normale</dt><dd class="font-medium text-ink">{{ $capteur->seuil_min }} – {{ $capteur->seuil_max }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-muted">Installé le</dt><dd class="font-medium text-ink">{{ $capteur->date_installation?->format('d/m/Y') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-muted">Réseau</dt><dd class="font-medium text-ink text-right">{{ $capteur->reseauEau?->type ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-muted">Zone</dt><dd class="font-medium text-ink">{{ $capteur->reseauEau?->zone?->nom ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="bg-white border border-border rounded-xl p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display text-base font-bold text-ink">Évolution des derniers relevés</h2>
                <span class="flex items-center gap-2 text-xs text-muted">
                    <span class="inline-block w-3 h-3 rounded-sm bg-success/15 border border-success/40"></span> Plage normale
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-alert ml-2"></span> Hors seuil
                </span>
            </div>
            @if ($chart)
                <svg viewBox="0 0 {{ $W }} {{ $H }}" class="w-full h-auto" role="img" aria-label="Graphique des derniers relevés">
                    <rect x="{{ $pad }}" y="{{ $chart['bandTop'] }}" width="{{ $W - 2 * $pad }}" height="{{ $chart['bandBottom'] - $chart['bandTop'] }}"
                          fill="rgb(var(--color-success-rgb) / 0.1)" stroke="rgb(var(--color-success-rgb) / 0.35)" stroke-dasharray="4 3" />
                    <polyline points="{{ $chart['points'] }}" fill="none" stroke="rgb(var(--color-primary-rgb))" stroke-width="2" stroke-linejoin="round" />
                    @foreach ($chart['dots'] as $dot)
                        <circle cx="{{ $dot['x'] }}" cy="{{ $dot['y'] }}" r="{{ $dot['alerte'] ? 4.5 : 3 }}"
                                fill="{{ $dot['alerte'] ? 'rgb(var(--color-alert-rgb))' : 'rgb(var(--color-primary-rgb))' }}">
                            <title>{{ $dot['titre'] }}</title>
                        </circle>
                    @endforeach
                    <text x="{{ $W - $pad }}" y="{{ $chart['bandTop'] - 5 }}" text-anchor="end" font-size="11" fill="rgb(var(--color-muted-rgb))">max {{ $capteur->seuil_max }}</text>
                    <text x="{{ $W - $pad }}" y="{{ $chart['bandBottom'] + 14 }}" text-anchor="end" font-size="11" fill="rgb(var(--color-muted-rgb))">min {{ $capteur->seuil_min }}</text>
                </svg>
            @else
                <x-empty-state icon="chart" title="Pas assez de données" description="Au moins deux relevés sont nécessaires pour tracer le graphique." />
            @endif
        </div>
    </div>

    {{-- Relevés du capteur (relation Capteur 1-N Releve) --}}
    <div class="bg-white border border-border rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 pt-5 pb-4">
            <h2 class="font-display text-lg font-bold text-ink">Relevés</h2>
            <a href="{{ route('admin.releves.index', ['capteur_id' => $capteur->id]) }}" class="text-sm font-medium text-primary hover:underline">Voir tout</a>
        </div>

        @if ($releves->isEmpty())
            <x-empty-state icon="chart" title="Aucun relevé" description="Les relevés de ce capteur apparaîtront ici." />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-y border-border text-left text-muted">
                            <th class="px-6 py-2.5 font-medium">Date</th>
                            <th class="px-6 py-2.5 font-medium">Valeur</th>
                            <th class="px-6 py-2.5 font-medium">État</th>
                            <th class="px-6 py-2.5 font-medium">Remarque</th>
                            <th class="px-6 py-2.5 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($releves as $releve)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3 text-ink">{{ $releve->date_releve->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-3 font-medium {{ $releve->hors_seuil ? 'text-alert-strong' : 'text-ink' }}">{{ $releve->valeur }} {{ $capteur->unite }}</td>
                                <td class="px-6 py-3">
                                    <x-badge :color="$releve->hors_seuil ? 'alert' : 'success'">{{ $releve->hors_seuil ? 'Hors seuil' : 'Normal' }}</x-badge>
                                </td>
                                <td class="px-6 py-3 text-ink/80">{{ $releve->remarque ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.releves.edit', $releve) }}" class="text-muted hover:text-primary" title="Modifier"><x-icon.pencil class="w-4 h-4" /></a>
                                        <form action="{{ route('admin.releves.destroy', $releve) }}" method="POST" onsubmit="return confirm('Supprimer ce relevé ?');">
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
            @if ($releves->hasPages())
                <div class="px-6 py-4 border-t border-border">{{ $releves->links() }}</div>
            @endif
        @endif
    </div>
@endsection
