@extends('layouts.back')

@section('title', 'Relevés')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display text-xl font-bold text-ink">Relevés</h1>
        <x-button href="{{ route('admin.releves.create', request()->only('capteur_id')) }}" variant="primary">
            <x-icon.plus class="w-4 h-4" /> Ajouter
        </x-button>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <x-input-label for="capteur_id" value="Capteur" class="!text-xs !text-muted" />
            <x-select id="capteur_id" name="capteur_id" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous les capteurs</option>
                @foreach ($capteurs as $capteur)
                    <option value="{{ $capteur->id }}" @selected(request('capteur_id') == $capteur->id)>{{ $capteur->code }}</option>
                @endforeach
            </x-select>
        </div>
        <div>
            <x-input-label for="etat" value="État" class="!text-xs !text-muted" />
            <x-select id="etat" name="etat" class="!w-auto !py-2 !pr-9" onchange="this.form.submit()">
                <option value="">Tous</option>
                <option value="normal" @selected(request('etat') === 'normal')>Normal</option>
                <option value="anomalie" @selected(request('etat') === 'anomalie')>Hors seuil</option>
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
            <a href="{{ route('admin.releves.index') }}" class="text-sm font-medium text-muted hover:text-ink pb-2">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white border border-border">
        @if ($releves->isEmpty())
            <x-empty-state icon="chart" title="Aucun relevé" description="Aucun relevé ne correspond à ces critères.">
                <x-slot:action>
                    <x-button href="{{ route('admin.releves.create') }}" variant="primary">Ajouter un relevé</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border text-left text-muted">
                            <th class="px-6 py-3 font-medium">Date</th>
                            <th class="px-6 py-3 font-medium">Capteur</th>
                            <th class="px-6 py-3 font-medium">Réseau</th>
                            <th class="px-6 py-3 font-medium">Valeur</th>
                            <th class="px-6 py-3 font-medium">État</th>
                            <th class="px-6 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($releves as $releve)
                            <tr class="border-b border-border last:border-b-0 hover:bg-bg">
                                <td class="px-6 py-3.5 text-ink whitespace-nowrap">
                                    <a href="{{ route('admin.releves.show', $releve) }}" class="hover:text-primary">{{ $releve->date_releve->format('d/m/Y H:i') }}</a>
                                </td>
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('admin.capteurs.show', $releve->capteur_id) }}" class="font-medium text-ink hover:text-primary">{{ $releve->capteur?->code }}</a>
                                    <p class="text-xs text-muted">{{ $releve->capteur?->type_label }}</p>
                                </td>
                                <td class="px-6 py-3.5 text-ink/80">{{ $releve->capteur?->reseauEau?->type ?? '—' }}</td>
                                <td class="px-6 py-3.5 font-medium whitespace-nowrap {{ $releve->hors_seuil ? 'text-alert-strong' : 'text-ink' }}">
                                    {{ $releve->valeur }} {{ $releve->capteur?->unite }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <x-badge :color="$releve->hors_seuil ? 'alert' : 'success'">{{ $releve->hors_seuil ? 'Hors seuil' : 'Normal' }}</x-badge>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.releves.show', $releve) }}" class="text-muted hover:text-primary" title="Voir"><x-icon.eye class="w-4 h-4" /></a>
                                        <a href="{{ route('admin.releves.edit', $releve) }}" class="text-muted hover:text-primary" title="Modifier"><x-icon.pencil class="w-4 h-4" /></a>
                                        <form action="{{ route('admin.releves.destroy', $releve) }}" method="POST" onsubmit="return confirm('Supprimer ce relevé ?');">
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

    @if ($releves->hasPages())
        <div class="mt-4">{{ $releves->links() }}</div>
    @endif
@endsection
