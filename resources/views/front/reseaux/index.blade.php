@extends('layouts.front')

@section('title', 'Réseaux')

@php
    $etatBadge = ['bon' => 'success', 'degrade' => 'warning', 'hors_service' => 'alert'];
    $etatLabel = ['bon' => 'Bon', 'degrade' => 'Dégradé', 'hors_service' => 'Hors service'];
@endphp

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-12">
        <h1 class="font-display text-2xl font-bold text-ink mb-1">Réseaux</h1>
        <p class="text-muted mb-8">État des infrastructures d'eau potable surveillées par zone.</p>

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
                <x-input-label for="etat" value="État" class="mb-1.5" />
                <x-select id="etat" name="etat" onchange="this.form.submit()">
                    <option value="">Tous les états</option>
                    <option value="bon" @selected(request('etat') === 'bon')>Bon</option>
                    <option value="degrade" @selected(request('etat') === 'degrade')>Dégradé</option>
                    <option value="hors_service" @selected(request('etat') === 'hors_service')>Hors service</option>
                </x-select>
            </div>

            @if (request()->filled('zone_id') || request()->filled('etat'))
                <a href="{{ route('reseaux.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2.5">Réinitialiser</a>
            @endif
        </form>

        @if ($reseaux->isEmpty())
            <div class="bg-white border border-border rounded-xl">
                <x-empty-state icon="share" title="Aucun réseau" description="Aucun réseau ne correspond à ces critères." />
            </div>
        @else
            <div class="bg-white border border-border rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left text-muted">
                                <th class="px-6 py-3 font-medium">Type</th>
                                <th class="px-6 py-3 font-medium">Zone</th>
                                <th class="px-6 py-3 font-medium">Commune</th>
                                <th class="px-6 py-3 font-medium">État</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reseaux as $reseau)
                                <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                    <td class="px-6 py-3.5 font-medium text-ink">{{ $reseau->type }}</td>
                                    <td class="px-6 py-3.5 text-ink/80">{{ $reseau->zone?->nom ?? '—' }}</td>
                                    <td class="px-6 py-3.5 text-ink/80">{{ $reseau->zone?->commune ?? '—' }}</td>
                                    <td class="px-6 py-3.5">
                                        <x-badge :color="$etatBadge[$reseau->etat] ?? 'neutral'">{{ $etatLabel[$reseau->etat] ?? ucfirst($reseau->etat) }}</x-badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $reseaux->links() }}
            </div>
        @endif
    </div>
@endsection
