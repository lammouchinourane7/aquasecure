{{-- Champs partagés entre la création et la modification d'un incident --}}
<div class="space-y-5">
    <div>
        <x-input-label for="reseau_id" value="Réseau concerné" />
        <x-select id="reseau_id" name="reseau_id" required>
            <option value="">Sélectionnez un réseau</option>
            @foreach ($reseaux as $reseau)
                <option value="{{ $reseau->id }}" @selected(old('reseau_id', $incident->reseau_id) == $reseau->id)>
                    {{ ucfirst($reseau->type) }} — {{ $reseau->zone?->nom }}
                </option>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('reseau_id')" />
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="type" value="Type d'incident" />
            <x-select id="type" name="type" required>
                <option value="">Sélectionnez un type</option>
                <option value="fuite" @selected(old('type', $incident->type) === 'fuite')>Fuite</option>
                <option value="contamination" @selected(old('type', $incident->type) === 'contamination')>Contamination</option>
                <option value="coupure" @selected(old('type', $incident->type) === 'coupure')>Coupure</option>
            </x-select>
            <x-input-error :messages="$errors->get('type')" />
        </div>

        <div>
            <x-input-label for="gravite" value="Niveau de gravité" />
            <x-select id="gravite" name="gravite" required>
                <option value="">Sélectionnez la gravité</option>
                <option value="faible" @selected(old('gravite', $incident->gravite) === 'faible')>Faible</option>
                <option value="moyenne" @selected(old('gravite', $incident->gravite) === 'moyenne')>Moyenne</option>
                <option value="critique" @selected(old('gravite', $incident->gravite) === 'critique')>Critique</option>
            </x-select>
            <x-input-error :messages="$errors->get('gravite')" />
        </div>
    </div>

    <div>
        <x-input-label for="date_signalement" value="Date de constat / signalement" />
        <x-text-input id="date_signalement" name="date_signalement" type="date"
                      max="{{ now()->format('Y-m-d') }}"
                      :value="old('date_signalement', $incident->date_signalement?->format('Y-m-d'))" required />
        <x-input-error :messages="$errors->get('date_signalement')" />
    </div>
</div>
