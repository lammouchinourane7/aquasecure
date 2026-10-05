@extends('layouts.back')

@section('title', 'Modifier la zone')

@section('content')
    <h1 class="font-display text-xl font-bold text-ink mb-6">Modifier la zone</h1>

    <div class="bg-white border border-border rounded-xl p-6 sm:p-8 max-w-xl">
        <form method="POST" action="{{ route('admin.zones.update', $zone) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="nom" value="Nom" />
                <x-text-input id="nom" name="nom" :value="old('nom', $zone->nom)" required autofocus />
                <x-input-error :messages="$errors->get('nom')" />
            </div>

            <div>
                <x-input-label for="commune" value="Commune" />
                <x-text-input id="commune" name="commune" :value="old('commune', $zone->commune)" required />
                <x-input-error :messages="$errors->get('commune')" />
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" variant="primary">Enregistrer</x-button>
                <a href="{{ route('admin.zones.index') }}" class="text-sm font-medium text-muted hover:text-ink">Annuler</a>
            </div>
        </form>
    </div>
@endsection
