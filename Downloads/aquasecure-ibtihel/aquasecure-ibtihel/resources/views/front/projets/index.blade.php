@extends('layouts.front')

@section('title', 'Projets de rénovation')

@use('App\Models\ProjetRenovation')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-12">
        <h1 class="font-display text-2xl font-bold text-ink mb-1">Projets de rénovation des réseaux</h1>
        <p class="text-muted mb-8">Suivi transparent des chantiers de modernisation et de décontamination de nos infrastructures d'eau potable.</p>

        {{-- Statistiques clés --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="border-l-4 border-primary pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $resume['total_projets'] }}</span>
                <p class="text-sm text-muted mt-1">projets engagés</p>
            </div>
            <div class="border-l-4 border-warning pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $resume['en_cours'] }}</span>
                <p class="text-sm text-muted mt-1">chantiers en cours d'exécution</p>
            </div>
            <div class="border-l-4 border-success pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $resume['termines'] }}</span>
                <p class="text-sm text-muted mt-1">projets achevés avec succès</p>
            </div>
            <div class="border-l-4 border-primary-strong pl-4">
                <span class="font-display text-3xl font-bold text-ink">{{ number_format($resume['total_budget'], 0, ',', ' ') }}</span>
                <p class="text-sm text-muted mt-1">TND mobilisés au total</p>
            </div>
        </div>

        {{-- Filtres --}}
        <form method="GET" class="flex flex-wrap items-end gap-4 mb-6">
            <div class="flex-1 min-w-[200px]">
                <x-input-label for="q" value="Recherche" class="mb-1.5" />
                <x-text-input id="q" name="q" :value="request('q')" placeholder="Rechercher un projet..." class="!py-2" />
            </div>

            <div class="w-48">
                <x-input-label for="zone_id" value="Zone" class="mb-1.5" />
                <x-select id="zone_id" name="zone_id" onchange="this.form.submit()">
                    <option value="">Toutes les zones</option>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}" @selected(request('zone_id') == $zone->id)>{{ $zone->nom }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="w-48">
                <x-input-label for="statut" value="Statut" class="mb-1.5" />
                <x-select id="statut" name="statut" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    @foreach (ProjetRenovation::STATUTS as $key => $label)
                        <option value="{{ $key }}" @selected(request('statut') === $key)>{{ $label }}</option>
                    @endforeach
                </x-select>
            </div>

            <x-button type="submit" variant="secondary" class="!py-2">Filtrer</x-button>

            @if (request()->collect()->except('page')->filter()->isNotEmpty())
                <a href="{{ route('projets.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2.5">Réinitialiser</a>
            @endif
        </form>

        @if ($projets->isEmpty())
            <div class="bg-white border border-border rounded-xl">
                <x-empty-state icon="building" title="Aucun projet trouvé" description="Aucun projet de rénovation ne correspond à ces critères." />
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projets as $projet)
                    <div class="bg-white border border-border rounded-xl p-5 flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <x-badge :color="$projet->statut_badge_color">{{ $projet->statut_label }}</x-badge>
                                <span class="text-xs text-muted font-medium">{{ $projet->reseauEau?->zone?->nom }}</span>
                            </div>

                            <h3 class="font-display text-base font-bold text-ink mb-2">
                                {{ $projet->titre }}
                            </h3>

                            <p class="text-xs text-muted mb-4">
                                Réseau concerné : <span class="font-medium text-ink">{{ $projet->reseauEau?->type }}</span>
                                ({{ $projet->reseauEau?->zone?->commune }})
                            </p>
                        </div>

                        <div>
                            {{-- Avancement indicatif --}}
                            @php
                                $progress = match ($projet->statut) {
                                    'termine' => 100,
                                    'en_cours' => 60,
                                    'planifie' => 15,
                                    default => 0,
                                };
                                $barColor = match ($projet->statut) {
                                    'termine' => 'bg-success',
                                    'en_cours' => 'bg-primary',
                                    default => 'bg-warning',
                                };
                            @endphp
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-muted mb-1">
                                    <span>Avancement</span>
                                    <span class="font-semibold text-ink">{{ $progress }} %</span>
                                </div>
                                <div class="w-full bg-border rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            {{-- Financement --}}
                            <div class="border-t border-border pt-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-muted">Budget alloué</span>
                                    <span class="font-display font-bold text-ink text-sm">
                                        {{ number_format($projet->financements_sum_montant ?? 0, 2, ',', ' ') }} TND
                                    </span>
                                </div>

                                @if ($projet->financements->isNotEmpty())
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach ($projet->financements as $f)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium bg-bg text-ink/80 px-2 py-0.5 rounded border border-border">
                                                <x-icon.coin class="w-3 h-3 text-primary" />
                                                {{ $f->source_label }} : {{ number_format($f->montant, 0, ',', ' ') }} DT
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-muted italic mt-1">Recherche de financements en cours</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">{{ $projets->links() }}</div>
        @endif
    </div>
@endsection
