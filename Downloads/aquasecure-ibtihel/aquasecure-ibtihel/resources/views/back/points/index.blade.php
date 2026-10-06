@extends('layouts.back')

@section('title', 'Points de prélèvement')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display text-xl font-bold text-ink">Points de prélèvement</h1>
        <x-button href="{{ route('admin.points.create') }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Ajouter
        </x-button>
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative w-full sm:w-64">
            <x-icon.search class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2" />
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Code, nom ou adresse..."
                   class="w-full rounded-lg border border-border bg-white pl-9 pr-4 py-2 text-sm text-ink focus:border-primary focus:ring-4 focus:ring-primary/15 transition">
        </div>
        <x-select name="type_point" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
            <option value="">Tous les types</option>
            @foreach (\App\Models\PointPrelevement::TYPES as $key => $label)
                <option value="{{ $key }}" @selected(request('type_point') === $key)>{{ $label }}</option>
            @endforeach
        </x-select>
        <x-select name="reseau_id" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
            <option value="">Tous les réseaux</option>
            @foreach ($reseaux as $reseau)
                <option value="{{ $reseau->id }}" @selected(request('reseau_id') == $reseau->id)>{{ $reseau->type }}</option>
            @endforeach
        </x-select>
        <x-select name="actif" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
            <option value="">Actifs et inactifs</option>
            <option value="1" @selected(request('actif') === '1')>Actifs</option>
            <option value="0" @selected(request('actif') === '0')>Inactifs</option>
        </x-select>
        @if (request()->collect()->except('page')->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty())
            <a href="{{ route('admin.points.index') }}" class="text-sm font-medium text-muted hover:text-ink">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white border border-border">
        @if ($points->isEmpty())
            <x-empty-state icon="droplet" title="Aucun point de prélèvement" description="Aucun point ne correspond à ces critères.">
                <x-slot:action>
                    <x-button href="{{ route('admin.points.create') }}" variant="primary">Ajouter un point</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted">
                            <th class="px-5 py-3 font-medium">Point</th>
                            <th class="px-5 py-3 font-medium">Réseau</th>
                            <th class="px-5 py-3 font-medium">Dernière analyse</th>
                            <th class="px-5 py-3 font-medium">Analyses</th>
                            <th class="px-5 py-3 font-medium">État</th>
                            <th class="px-5 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($points as $point)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-5 py-3.5 min-w-[12rem]">
                                    <a href="{{ route('admin.points.show', $point) }}" class="font-medium text-ink hover:text-primary">{{ $point->code }}</a>
                                    <p class="text-xs text-muted">{{ $point->nom }} · {{ $point->type_label }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-ink/80 min-w-[12rem]">
                                    {{ $point->reseauEau?->type ?? '—' }}
                                    <p class="text-xs text-muted">{{ $point->reseauEau?->zone?->nom }}</p>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($point->derniereAnalyse)
                                        <x-badge :color="$point->derniereAnalyse->conforme ? 'success' : 'alert'">
                                            {{ $point->derniereAnalyse->conforme ? 'Conforme' : 'Non conforme' }}
                                        </x-badge>
                                        <p class="text-xs text-muted mt-1">{{ $point->derniereAnalyse->date_prelevement->format('d/m/Y') }}</p>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-ink/80 whitespace-nowrap">
                                    {{ $point->analyses_count }}
                                    @if ($point->non_conformes_count > 0)
                                        <p class="text-xs font-semibold text-alert-strong">{{ $point->non_conformes_count }} non conforme(s)</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <x-badge :color="$point->actif ? 'primary' : 'neutral'">{{ $point->actif ? 'Actif' : 'Inactif' }}</x-badge>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.points.show', $point) }}" class="text-muted hover:text-primary" title="Voir"><x-icon.eye class="w-4 h-4" /></a>
                                        <a href="{{ route('admin.points.edit', $point) }}" class="text-muted hover:text-primary" title="Modifier"><x-icon.pencil class="w-4 h-4" /></a>
                                        <form action="{{ route('admin.points.destroy', $point) }}" method="POST"
                                              onsubmit="return confirm('Supprimer le point {{ $point->code }} ? Ses {{ $point->analyses_count }} analyse(s) seront aussi supprimées.');">
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
        @endif
    </div>

    @if ($points->hasPages())
        <div class="mt-4">{{ $points->links() }}</div>
    @endif
@endsection
