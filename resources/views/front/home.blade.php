@extends('layouts.front')

@section('title', 'Accueil')

@php
    $couleurEtat = ['bon' => 'bg-success', 'degrade' => 'bg-warning', 'hors_service' => 'bg-alert'];
    $libelleEtat = ['bon' => 'En bon état', 'degrade' => 'Dégradé', 'hors_service' => 'Hors service'];
    $total = array_sum($etats);
@endphp

@section('content')
    <section class="max-w-6xl mx-auto px-6 pt-10 pb-6">
        <h1 class="font-display text-[34px] sm:text-[40px] leading-[1.1] font-bold text-ink max-w-2xl">
            Où en est le réseau d'eau de votre quartier&nbsp;?
        </h1>
        <p class="text-muted mt-3 max-w-xl">
            Chaque trait représente un ouvrage du réseau (canalisation, réservoir ou station),
            coloré selon son dernier état connu.
        </p>
    </section>

    <section class="max-w-6xl mx-auto px-6 pb-14 grid lg:grid-cols-[1fr_300px] gap-8 items-start">
        {{-- État du réseau, zone par zone --}}
        <div class="bg-white border border-border rounded-xl">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 px-5 py-3 border-b border-border text-[13px] text-muted">
                @foreach ($libelleEtat as $cle => $libelle)
                    <span class="flex items-center gap-2">
                        <span class="inline-block w-5 h-2 rounded-sm {{ $couleurEtat[$cle] }}"></span>
                        {{ $libelle }} ({{ $etats[$cle] }})
                    </span>
                @endforeach
            </div>

            @forelse ($zones as $zone)
                <div class="grid sm:grid-cols-[180px_1fr] gap-x-6 gap-y-2 px-5 py-4 border-b border-border last:border-b-0">
                    <div>
                        <p class="font-semibold text-ink leading-snug">{{ $zone->nom }}</p>
                        <p class="text-[13px] text-muted">{{ $zone->commune }}</p>
                    </div>

                    @if ($zone->reseauEaus->isEmpty())
                        <p class="text-[13px] text-muted self-center">Aucun ouvrage enregistré.</p>
                    @else
                        <ul class="flex flex-col gap-1.5 self-center">
                            @foreach ($zone->reseauEaus as $reseau)
                                <li class="grid grid-cols-[36px_1fr] sm:grid-cols-[72px_1fr] items-baseline gap-3 text-[13.5px]">
                                    <span class="h-2 rounded-sm translate-y-[-2px] {{ $couleurEtat[$reseau->etat] ?? 'bg-muted' }}" title="{{ $libelleEtat[$reseau->etat] ?? $reseau->etat }}"></span>
                                    <span class="text-ink/85">
                                        {{ $reseau->type }}
                                        @if ($reseau->etat !== 'bon')
                                            <span class="{{ $reseau->etat === 'hors_service' ? 'text-alert-strong' : 'text-warning-strong' }}">({{ mb_strtolower($libelleEtat[$reseau->etat] ?? $reseau->etat) }})</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @empty
                <x-empty-state icon="map" title="Aucune zone" description="Les zones apparaîtront ici dès qu'elles seront ajoutées." />
            @endforelse
        </div>

        {{-- Résumé des autres modules --}}
        <aside class="space-y-3">
            <a href="{{ route('qualite.index') }}" class="block bg-white border border-border rounded-xl px-5 py-4 hover:border-primary/60 transition-colors">
                <p class="text-[13px] text-muted">Qualité de l'eau, 3 derniers mois</p>
                <p class="font-display text-[28px] font-semibold text-ink leading-tight mt-0.5">
                    {{ $resume['conformite'] !== null ? $resume['conformite'].' %' : '—' }}
                    <span class="text-[15px] font-normal text-muted">d'analyses conformes</span>
                </p>
                @if ($resume['derniere_analyse'])
                    <p class="text-[13px] text-muted mt-1">Dernier prélèvement le {{ \Illuminate\Support\Carbon::parse($resume['derniere_analyse'])->format('d/m/Y') }}</p>
                @endif
            </a>

            <a href="{{ route('surveillance.index') }}" class="block bg-white border border-border rounded-xl px-5 py-4 hover:border-primary/60 transition-colors">
                <p class="text-[13px] text-muted">Capteurs, 7 derniers jours</p>
                <p class="font-display text-[28px] font-semibold leading-tight mt-0.5 {{ $resume['alertes'] > 0 ? 'text-alert-strong' : 'text-ink' }}">
                    {{ $resume['alertes'] }}
                    <span class="text-[15px] font-normal text-muted">mesure{{ $resume['alertes'] > 1 ? 's' : '' }} anormale{{ $resume['alertes'] > 1 ? 's' : '' }}</span>
                </p>
            </a>

            <a href="{{ route('projets.index') }}" class="block bg-white border border-border rounded-xl px-5 py-4 hover:border-primary/60 transition-colors">
                <p class="text-[13px] text-muted">Rénovation</p>
                <p class="font-display text-[28px] font-semibold text-ink leading-tight mt-0.5">
                    {{ $resume['chantiers'] }}
                    <span class="text-[15px] font-normal text-muted">chantier{{ $resume['chantiers'] > 1 ? 's' : '' }} en cours</span>
                </p>
            </a>

            <a href="{{ route('signalements.index') }}" class="block rounded-xl px-5 py-4 bg-conduite text-white hover:bg-primary-strong transition-colors">
                <p class="font-semibold">Une fuite, une eau trouble&nbsp;?</p>
                <p class="text-[13.5px] text-white/75 mt-0.5">Signalez-le aux services des eaux.</p>
            </a>
        </aside>
    </section>
@endsection
