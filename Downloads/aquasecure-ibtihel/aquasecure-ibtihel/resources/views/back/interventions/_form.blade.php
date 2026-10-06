{{-- Champs partagés entre la création et la modification d'une intervention --}}
@php
    $typeLabel = ['fuite' => 'Fuite', 'contamination' => 'Contamination', 'coupure' => 'Coupure'];
@endphp

<div class="space-y-5">
    <div>
        <x-input-label for="incident_id" value="Incident concerné" />
        <x-select id="incident_id" name="incident_id" required>
            <option value="">Sélectionnez un incident</option>
            @foreach ($incidents as $inc)
                <option value="{{ $inc->id }}" @selected(old('incident_id', $intervention->incident_id) == $inc->id)>
                    Incident #{{ $inc->id }} — {{ $typeLabel[$inc->type] ?? ucfirst($inc->type) }} ({{ $inc->reseauEau?->type ?? 'Sans réseau' }})
                </option>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('incident_id')" />
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="statut" value="Statut de l'intervention" />
            <x-select id="statut" name="statut" required>
                <option value="">Sélectionnez un statut</option>
                <option value="planifiee" @selected(old('statut', $intervention->statut) === 'planifiee')>Planifiée</option>
                <option value="en_cours" @selected(old('statut', $intervention->statut) === 'en_cours')>En cours</option>
                <option value="terminee" @selected(old('statut', $intervention->statut) === 'terminee')>Terminée</option>
            </x-select>
            <x-input-error :messages="$errors->get('statut')" />
        </div>

        <div>
            <x-input-label for="date_intervention" value="Date d'intervention" />
            <x-text-input id="date_intervention" name="date_intervention" type="date"
                          :value="old('date_intervention', $intervention->date_intervention?->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('date_intervention')" />
        </div>
    </div>
</div>
