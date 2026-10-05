@extends('layouts.back')

@section('title', 'Fiche point de prélèvement')

@use('App\Models\AnalyseQualite')

@php
    $taux = $total ? round($conformes / $total * 100) : null;
@endphp

@section('content')
    <a href="{{ route('admin.points.index') }}" class="text-sm text-muted hover:text-ink">&larr; Points de prélèvement</a>

    <div class="flex flex-wrap items-start justify-between gap-4 mt-3 mb-8">
        <div>
            <h1 class="font-display text-xl font-bold text-ink">{{ $point->code }} · {{ $point->nom }}</h1>
            <p class="text-sm text-muted mt-1">
                {{ $point->type_label }} · {{ $point->adresse }} ·
                <a href="{{ route('admin.reseaux.show', $point->reseau_id) }}" class="hover:text-primary">{{ $point->reseauEau?->type }}</a>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-badge :color="$point->actif ? 'primary' : 'neutral'">{{ $point->actif ? 'Actif' : 'Inactif' }}</x-badge>
            @if ($point->actif)
                <x-button href="{{ route('admin.analyses.create', ['point_id' => $point->id]) }}" variant="primary">
                    <x-icon.plus class="w-4 h-4" /> Nouvelle analyse
                </x-button>
            @endif
            <x-button href="{{ route('admin.points.edit', $point) }}" variant="secondary">Modifier</x-button>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="border-l-4 border-primary pl-4">
            <span class="font-display text-3xl font-bold text-ink">{{ $total }}</span>
            <p class="text-sm text-muted mt-1">Analyses</p>
        </div>
        <div class="border-l-4 border-success pl-4">
            <span class="font-display text-3xl font-bold text-ink">{{ $taux !== null ? $taux.' %' : '—' }}</span>
            <p class="text-sm text-muted mt-1">Taux de conformité</p>
        </div>
        <div class="border-l-4 border-alert pl-4">
            <span class="font-display text-3xl font-bold text-ink">{{ $total - $conformes }}</span>
            <p class="text-sm text-muted mt-1">Analyses non conformes</p>
        </div>
        <div class="border-l-4 border-border pl-4">
            <span class="font-display text-3xl font-bold text-ink">{{ $analyses->first()?->date_prelevement->format('d/m/Y') ?? '—' }}</span>
            <p class="text-sm text-muted mt-1">Dernier prélèvement</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-8">
        {{-- Moyennes de chaque paramètre comparées aux normes de potabilité --}}
        <div class="bg-white border border-border rounded-xl p-6">
            <h2 class="font-display text-base font-bold text-ink mb-4">Moyennes des paramètres</h2>
            @if ($moyennes)
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-muted border-b border-border">
                            <th class="pb-2 font-medium">Paramètre</th>
                            <th class="pb-2 font-medium">Moyenne</th>
                            <th class="pb-2 font-medium">Norme</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (AnalyseQualite::NORMES as $param => $norme)
                            @php $ok = AnalyseQualite::respecteNorme($param, (float) $moyennes->{$param}); @endphp
                            <tr class="border-b border-border last:border-b-0">
                                <td class="py-2.5 text-ink">{{ $norme['label'] }}</td>
                                <td class="py-2.5 font-medium {{ $ok ? 'text-ink' : 'text-alert-strong' }}">{{ round($moyennes->{$param}, 2) }} {{ $norme['unite'] }}</td>
                                <td class="py-2.5 text-muted">{{ AnalyseQualite::normeTexte($param) }}</td>
                                <td class="py-2.5 text-right">
                                    <x-badge :color="$ok ? 'success' : 'alert'">{{ $ok ? 'OK' : 'Hors norme' }}</x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <x-empty-state icon="flask" title="Aucune analyse" description="Ajoutez une première analyse pour ce point." />
            @endif
        </div>

        {{-- Localisation sur carte (OpenStreetMap) --}}
        <div class="bg-white border border-border rounded-xl p-6">
            <h2 class="font-display text-base font-bold text-ink mb-4">Localisation</h2>
            @if ($point->aDesCoordonnees())
                @php
                    $d = 0.006;
                    $bbox = ($point->longitude - $d).','.($point->latitude - $d).','.($point->longitude + $d).','.($point->latitude + $d);
                @endphp
                <iframe class="w-full h-64 rounded-lg border border-border" loading="lazy"
                        src="https://www.openstreetmap.org/export/embed.html?bbox={{ $bbox }}&layer=mapnik&marker={{ $point->latitude }},{{ $point->longitude }}"></iframe>
                <p class="text-xs text-muted mt-2">{{ $point->latitude }}, {{ $point->longitude }}</p>
            @else
                <x-empty-state icon="map" title="Pas de coordonnées" description="Ajoutez la latitude et la longitude pour afficher la carte." />
            @endif
        </div>
    </div>

    {{-- Analyses du point (relation PointPrelevement 1-N AnalyseQualite) --}}
    <div class="bg-white border border-border rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 pt-5 pb-4">
            <h2 class="font-display text-lg font-bold text-ink">Historique des analyses</h2>
            <a href="{{ route('admin.analyses.index', ['point_id' => $point->id]) }}" class="text-sm font-medium text-primary hover:underline">Voir tout</a>
        </div>

        @if ($analyses->isEmpty())
            <x-empty-state icon="flask" title="Aucune analyse" description="Les analyses de ce point apparaîtront ici." />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-y border-border text-left text-muted">
                            <th class="px-6 py-2.5 font-medium">Date</th>
                            @foreach (AnalyseQualite::NORMES as $norme)
                                <th class="px-4 py-2.5 font-medium whitespace-nowrap">{{ $norme['label'] }}</th>
                            @endforeach
                            <th class="px-4 py-2.5 font-medium">Résultat</th>
                            <th class="px-6 py-2.5 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analyses as $analyse)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.analyses.show', $analyse) }}" class="text-ink hover:text-primary">{{ $analyse->date_prelevement->format('d/m/Y') }}</a>
                                </td>
                                @foreach (AnalyseQualite::NORMES as $param => $norme)
                                    <td class="px-4 py-3 {{ AnalyseQualite::respecteNorme($param, $analyse->{$param}) ? 'text-ink/80' : 'text-alert-strong font-semibold' }}">
                                        {{ $analyse->{$param} }}
                                    </td>
                                @endforeach
                                <td class="px-4 py-3">
                                    <x-badge :color="$analyse->conforme ? 'success' : 'alert'">{{ $analyse->conforme ? 'Conforme' : 'Non conforme' }}</x-badge>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.analyses.edit', $analyse) }}" class="text-muted hover:text-primary" title="Modifier"><x-icon.pencil class="w-4 h-4" /></a>
                                        <form action="{{ route('admin.analyses.destroy', $analyse) }}" method="POST" onsubmit="return confirm('Supprimer cette analyse ?');">
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
            @if ($analyses->hasPages())
                <div class="px-6 py-4 border-t border-border">{{ $analyses->links() }}</div>
            @endif
        @endif
    </div>
@endsection
