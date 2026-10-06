@extends('layouts.back')

@section('title', 'Modifier le réseau')

@section('content')
    <h1 class="font-display text-xl font-bold text-ink mb-6">Modifier le réseau</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-xl">
        <form method="POST" action="{{ route('admin.reseaux.update', $reseau) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="zone_id" value="Zone" />
                <x-select id="zone_id" name="zone_id" required>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}" @selected(old('zone_id', $reseau->zone_id) == $zone->id)>{{ $zone->nom }} ({{ $zone->commune }})</option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('zone_id')" />
            </div>

            <div>
                <x-input-label for="type" value="Type" />
                <x-text-input id="type" name="type" :value="old('type', $reseau->type)" required />
                <x-input-error :messages="$errors->get('type')" />
            </div>

            <div>
                <x-input-label for="etat" value="État" />
                <x-select id="etat" name="etat" required>
                    <option value="bon" @selected(old('etat', $reseau->etat) === 'bon')>Bon</option>
                    <option value="degrade" @selected(old('etat', $reseau->etat) === 'degrade')>Dégradé</option>
                    <option value="hors_service" @selected(old('etat', $reseau->etat) === 'hors_service')>Hors service</option>
                </x-select>
                <x-input-error :messages="$errors->get('etat')" />
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" variant="primary">Enregistrer</x-button>
                <a href="{{ route('admin.reseaux.index') }}" class="text-sm font-medium text-muted hover:text-ink">Annuler</a>
            </div>
        </form>
    </div>
@endsection
