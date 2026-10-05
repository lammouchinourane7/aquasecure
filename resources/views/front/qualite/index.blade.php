@extends('layouts.front')

@section('title', 'Qualité de l\'eau')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-12">
        <h1 class="font-display text-2xl font-bold text-ink mb-1">Qualité de l'eau</h1>
        <p class="text-muted mb-8">Résultats des dernières analyses de laboratoire aux points de prélèvement du réseau.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="border-l-4 border-primary pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $resume['points'] }}</span>
                <p class="text-sm text-muted mt-1">points de prélèvement suivis</p>
            </div>
            <div class="border-l-4 border-border pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $resume['analyses'] }}</span>
                <p class="text-sm text-muted mt-1">analyses ces 3 derniers mois</p>
            </div>
            <div class="border-l-4 border-success pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $resume['taux'] !== null ? $resume['taux'].' %' : '—' }}</span>
                <p class="text-sm text-muted mt-1">d'analyses conformes aux normes</p>
            </div>
        </div>

        <form method="GET" class="flex flex-wrap items-end gap-4 mb-6">
            <div class="w-56">
                <x-input-label for="zone_id" value="Zone" class="mb-1.5" />
                <x-select id="zone_id" name="zone_id" onchange="this.form.submit()">
                    <option value="">Toutes les zones</option>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}" @selected(request('zone_id') == $zone->id)>{{ $zone->nom }}</option>
                    @endforeach
                </x-select>
            </div>
            @if (request()->filled('zone_id'))
                <a href="{{ route('qualite.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2.5">Réinitialiser</a>
            @endif
        </form>

        @if ($points->isEmpty())
            <div class="bg-white border border-border rounded-xl">
                <x-empty-state icon="droplet" title="Aucun point de prélèvement" description="Aucun point ne correspond à ces critères." />
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($points as $point)
                    @php $a = $point->derniereAnalyse; @endphp
                    <div class="bg-white border border-border rounded-xl p-5 flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <p class="font-display font-bold text-ink">{{ $point->nom }}</p>
                                <p class="text-xs text-muted">{{ $point->type_label }} · {{ $point->adresse }}</p>
                            </div>
                            @if ($a)
                                <x-badge :color="$a->conforme ? 'success' : 'alert'" class="shrink-0">{{ $a->conforme ? 'Eau conforme' : 'Non conforme' }}</x-badge>
                            @else
                                <x-badge class="shrink-0">Pas d'analyse</x-badge>
                            @endif
                        </div>

                        @if ($a)
                            <dl class="grid grid-cols-3 gap-2 text-center my-2">
                                <div class="rounded-lg bg-bg py-2">
                                    <dt class="text-xs text-muted">pH</dt>
                                    <dd class="font-semibold {{ \App\Models\AnalyseQualite::respecteNorme('ph', $a->ph) ? 'text-ink' : 'text-alert-strong' }}">{{ $a->ph }}</dd>
                                </div>
                                <div class="rounded-lg bg-bg py-2">
                                    <dt class="text-xs text-muted">Chlore</dt>
                                    <dd class="font-semibold {{ \App\Models\AnalyseQualite::respecteNorme('chlore_residuel', $a->chlore_residuel) ? 'text-ink' : 'text-alert-strong' }}">{{ $a->chlore_residuel }}</dd>
                                </div>
                                <div class="rounded-lg bg-bg py-2">
                                    <dt class="text-xs text-muted">Turbidité</dt>
                                    <dd class="font-semibold {{ \App\Models\AnalyseQualite::respecteNorme('turbidite', $a->turbidite) ? 'text-ink' : 'text-alert-strong' }}">{{ $a->turbidite }}</dd>
                                </div>
                            </dl>
                            @unless ($a->conforme)
                                <p class="text-xs text-alert-strong">Hors norme : {{ implode(', ', $a->parametresNonConformes()) }}</p>
                            @endunless
                            <p class="text-xs text-muted mt-2 mb-4">Analyse du {{ $a->date_prelevement->format('d/m/Y') }}</p>
                        @endif

                        <div class="border-t border-border mt-auto pt-3 text-sm text-ink/80">
                            {{ $point->reseauEau?->type ?? '—' }}
                            <span class="text-muted">· {{ $point->reseauEau?->zone?->nom }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $points->links() }}</div>
        @endif
    </div>
@endsection
