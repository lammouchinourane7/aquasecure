@extends('layouts.back')

@section('title', 'Tableau de bord')

@php
    $etatBadge = ['bon' => 'success', 'degrade' => 'warning', 'hors_service' => 'alert'];
    $etatLabel = ['bon' => 'Bon', 'degrade' => 'Dégradé', 'hors_service' => 'Hors service'];
    $borderColor = ['primary' => 'border-primary', 'alert' => 'border-alert', 'warning' => 'border-warning', 'success' => 'border-success'];
@endphp

@section('content')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @foreach ($stats as $stat)
            <div class="border-l-4 {{ $borderColor[$stat['color']] ?? 'border-primary' }} pl-4">
                <span class="font-display text-4xl font-bold text-ink">{{ $stat['value'] }}</span>
                <p class="text-sm text-muted mt-1">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white border border-border rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-6 pt-5 pb-4">
            <h2 class="font-display text-lg font-bold text-ink">Derniers réseaux</h2>
            <a href="{{ route('admin.reseaux.index') }}" class="text-sm font-medium text-primary hover:underline">Voir tout</a>
        </div>

        @if ($derniersReseaux->isEmpty())
            <x-empty-state icon="share" title="Aucun réseau" description="Les réseaux ajoutés apparaîtront ici." />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-y border-border text-left text-muted">
                            <th class="px-6 py-2.5 font-medium">Type</th>
                            <th class="px-6 py-2.5 font-medium">Zone</th>
                            <th class="px-6 py-2.5 font-medium">État</th>
                            <th class="px-6 py-2.5 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($derniersReseaux as $reseau)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3 text-ink font-medium">{{ $reseau->type }}</td>
                                <td class="px-6 py-3 text-ink/80">{{ $reseau->zone?->nom ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <x-badge :color="$etatBadge[$reseau->etat] ?? 'neutral'">{{ $etatLabel[$reseau->etat] ?? ucfirst($reseau->etat) }}</x-badge>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.reseaux.show', $reseau) }}" class="text-primary hover:underline">Voir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
