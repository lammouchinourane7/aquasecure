@extends('layouts.front')

@section('title', 'Surveillance')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-12">
        <h1 class="font-display text-2xl font-bold text-ink mb-1">Surveillance du réseau</h1>
        <p class="text-muted mb-8">Capteurs installés sur les réseaux d'eau potable et dernières valeurs mesurées.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="border-l-4 border-success pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $resume['actifs'] }}</span>
                <p class="text-sm text-muted mt-1">capteurs en fonctionnement</p>
            </div>
            <div class="border-l-4 border-warning pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $resume['en_panne'] }}</span>
                <p class="text-sm text-muted mt-1">capteurs en panne ou hors service</p>
            </div>
            <div class="border-l-4 border-alert pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $resume['alertes_7j'] }}</span>
                <p class="text-sm text-muted mt-1">mesures anormales ces 7 derniers jours</p>
            </div>
        </div>

        <form method="GET" class="flex flex-wrap items-end gap-4 mb-6">
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
                <x-input-label for="type_mesure" value="Type de mesure" class="mb-1.5" />
                <x-select id="type_mesure" name="type_mesure" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    @foreach (\App\Models\Capteur::TYPES as $key => $t)
                        <option value="{{ $key }}" @selected(request('type_mesure') === $key)>{{ $t['label'] }}</option>
                    @endforeach
                </x-select>
            </div>
            @if (request()->filled('zone_id') || request()->filled('type_mesure'))
                <a href="{{ route('surveillance.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2.5">Réinitialiser</a>
            @endif
        </form>

        @if ($capteurs->isEmpty())
            <div class="bg-white border border-border rounded-xl">
                <x-empty-state icon="radar" title="Aucun capteur" description="Aucun capteur ne correspond à ces critères." />
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($capteurs as $capteur)
                    @php
                        $dernier = $capteur->dernierReleve;
                        if ($capteur->statut !== 'actif') {
                            [$etat, $couleur] = [$capteur->statut_label, 'neutral'];
                        } elseif (! $dernier) {
                            [$etat, $couleur] = ['Aucune mesure', 'neutral'];
                        } elseif ($dernier->hors_seuil) {
                            [$etat, $couleur] = ['Valeur anormale', 'alert'];
                        } else {
                            [$etat, $couleur] = ['Normal', 'success'];
                        }
                    @endphp
                    <div class="bg-white border border-border rounded-xl p-5">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div>
                                <p class="font-display font-bold text-ink">{{ $capteur->type_label }}</p>
                                <p class="text-xs text-muted">{{ $capteur->code }}</p>
                            </div>
                            <x-badge :color="$couleur">{{ $etat }}</x-badge>
                        </div>

                        <p class="font-display text-3xl font-bold {{ $dernier?->hors_seuil ? 'text-alert-strong' : 'text-ink' }}">
                            {{ $dernier ? $dernier->valeur : '—' }} <span class="text-base font-medium text-muted">{{ $capteur->unite }}</span>
                        </p>
                        <p class="text-xs text-muted mt-1">
                            Plage normale {{ $capteur->seuil_min }} – {{ $capteur->seuil_max }} {{ $capteur->unite }}
                            @if ($dernier) · {{ $dernier->date_releve->locale('fr')->diffForHumans() }} @endif
                        </p>

                        <div class="border-t border-border mt-4 pt-3 text-sm text-ink/80">
                            {{ $capteur->reseauEau?->type ?? '—' }}
                            <span class="text-muted">· {{ $capteur->reseauEau?->zone?->nom }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $capteurs->links() }}</div>
        @endif
    </div>
@endsection
