@extends('layouts.back')

@section('title', 'Réseaux')

@php
    $etatBadge = ['bon' => 'success', 'degrade' => 'warning', 'hors_service' => 'alert'];
    $etatLabel = ['bon' => 'Bon', 'degrade' => 'Dégradé', 'hors_service' => 'Hors service'];
@endphp

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display text-xl font-bold text-ink">Réseaux</h1>
        <x-button href="{{ route('admin.reseaux.create') }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Ajouter
        </x-button>
    </div>

    <form method="GET" class="mb-4">
        <div class="relative max-w-sm">
            <x-icon.search class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2" />
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un réseau..."
                   class="w-full rounded-lg border border-border bg-white pl-9 pr-4 py-2 text-sm text-ink focus:border-primary focus:ring-4 focus:ring-primary/15 transition">
        </div>
    </form>

    <div class="bg-white border border-border">
        @if ($reseaux->isEmpty())
            <x-empty-state icon="share" title="Aucun réseau" description="Commencez par ajouter un réseau d'eau.">
                <x-slot:action>
                    <x-button href="{{ route('admin.reseaux.create') }}" variant="primary">Ajouter un réseau</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted">
                            <th class="px-6 py-3 font-medium">Type</th>
                            <th class="px-6 py-3 font-medium">Zone</th>
                            <th class="px-6 py-3 font-medium">État</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reseaux as $reseau)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('admin.reseaux.show', $reseau) }}" class="font-medium text-ink hover:text-primary">{{ $reseau->type }}</a>
                                </td>
                                <td class="px-6 py-3.5 text-ink/80">{{ $reseau->zone?->nom ?? '—' }}</td>
                                <td class="px-6 py-3.5">
                                    <x-badge :color="$etatBadge[$reseau->etat] ?? 'neutral'">{{ $etatLabel[$reseau->etat] ?? ucfirst($reseau->etat) }}</x-badge>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.reseaux.edit', $reseau) }}" class="text-muted hover:text-primary" title="Modifier">
                                            <x-icon.pencil class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('admin.reseaux.destroy', $reseau) }}" method="POST" onsubmit="return confirm('Supprimer ce réseau ?');">
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

    @if ($reseaux->hasPages())
        <div class="mt-4">{{ $reseaux->links() }}</div>
    @endif
@endsection
