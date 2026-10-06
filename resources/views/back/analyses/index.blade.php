@extends('layouts.back')

@section('title', 'Analyses qualité')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display text-xl font-bold text-ink">Analyses qualité</h1>
        <x-button href="{{ route('admin.analyses.create', request()->only('point_id')) }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Ajouter
        </x-button>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <x-input-label for="point_id" value="Point" class="!text-xs !text-muted" />
            <x-select id="point_id" name="point_id" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous les points</option>
                @foreach ($points as $point)
                    <option value="{{ $point->id }}" @selected(request('point_id') == $point->id)>{{ $point->code }} — {{ $point->nom }}</option>
                @endforeach
            </x-select>
        </div>
        <div>
            <x-input-label for="conformite" value="Résultat" class="!text-xs !text-muted" />
            <x-select id="conformite" name="conformite" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous</option>
                <option value="conforme" @selected(request('conformite') === 'conforme')>Conformes</option>
                <option value="non_conforme" @selected(request('conformite') === 'non_conforme')>Non conformes</option>
            </x-select>
        </div>
        <div>
            <x-input-label for="laboratoire" value="Laboratoire" class="!text-xs !text-muted" />
            <x-select id="laboratoire" name="laboratoire" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous</option>
                @foreach ($laboratoires as $labo)
                    <option value="{{ $labo }}" @selected(request('laboratoire') === $labo)>{{ $labo }}</option>
                @endforeach
            </x-select>
        </div>
        <div>
            <x-input-label for="du" value="Du" class="!text-xs !text-muted" />
            <x-text-input id="du" name="du" type="date" :value="request('du')" class="!w-auto !py-2" onchange="this.form.submit()" />
        </div>
        <div>
            <x-input-label for="au" value="Au" class="!text-xs !text-muted" />
            <x-text-input id="au" name="au" type="date" :value="request('au')" class="!w-auto !py-2" onchange="this.form.submit()" />
        </div>
        @if (request()->collect()->except('page')->filter()->isNotEmpty())
            <a href="{{ route('admin.analyses.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white border border-border">
        @if ($analyses->isEmpty())
            <x-empty-state icon="flask" title="Aucune analyse" description="Aucune analyse ne correspond à ces critères.">
                <x-slot:action>
                    <x-button href="{{ route('admin.analyses.create') }}" variant="primary">Ajouter une analyse</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted">
                            <th class="px-6 py-3 font-medium">Date</th>
                            <th class="px-6 py-3 font-medium">Point</th>
                            <th class="px-6 py-3 font-medium">Laboratoire</th>
                            <th class="px-6 py-3 font-medium">Résultat</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analyses as $analyse)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <a href="{{ route('admin.analyses.show', $analyse) }}" class="text-ink hover:text-primary">{{ $analyse->date_prelevement->format('d/m/Y') }}</a>
                                </td>
                                <td class="px-6 py-3.5 min-w-[12rem]">
                                    <a href="{{ route('admin.points.show', $analyse->point_id) }}" class="font-medium text-ink hover:text-primary">{{ $analyse->point?->code }}</a>
                                    <p class="text-xs text-muted">{{ $analyse->point?->nom }} · {{ $analyse->point?->reseauEau?->type }}</p>
                                </td>
                                <td class="px-6 py-3.5 text-ink/80">{{ $analyse->laboratoire }}</td>
                                <td class="px-6 py-3.5">
                                    <x-badge :color="$analyse->conforme ? 'success' : 'alert'">{{ $analyse->conforme ? 'Conforme' : 'Non conforme' }}</x-badge>
                                    @unless ($analyse->conforme)
                                        <p class="text-xs text-alert-strong mt-1">{{ implode(', ', $analyse->parametresNonConformes()) }}</p>
                                    @endunless
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.analyses.show', $analyse) }}" class="text-muted hover:text-primary" title="Voir"><x-icon.eye class="w-4 h-4" /></a>
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
        @endif
    </div>

    @if ($analyses->hasPages())
        <div class="mt-4">{{ $analyses->links() }}</div>
    @endif
@endsection
