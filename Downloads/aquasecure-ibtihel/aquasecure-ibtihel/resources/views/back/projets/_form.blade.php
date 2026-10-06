@use('App\Models\ProjetRenovation')

<div class="space-y-5">
    <div>
        <x-input-label for="titre" value="Titre du projet" />
        <x-text-input id="titre" name="titre" type="text"
                      :value="old('titre', $projet->titre)"
                      placeholder="Ex : Réhabilitation de la conduite principale et modernisation des vannes"
                      required autofocus />
        <x-input-error :messages="$errors->get('titre')" />
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="reseau_id" value="Réseau d'eau concerné" />
            <x-select id="reseau_id" name="reseau_id" required>
                <option value="">Sélectionnez un réseau</option>
                @foreach ($reseaux as $reseau)
                    <option value="{{ $reseau->id }}" @selected(old('reseau_id', $projet->reseau_id) == $reseau->id)>
                        {{ $reseau->type }} — {{ $reseau->zone?->nom }} ({{ $reseau->zone?->commune }})
                    </option>
                @endforeach
            </x-select>
            <x-input-error :messages="$errors->get('reseau_id')" />
        </div>

        <div>
            <x-input-label for="statut" value="Statut du projet" />
            <x-select id="statut" name="statut" required>
                @foreach (ProjetRenovation::STATUTS as $key => $label)
                    <option value="{{ $key }}" @selected(old('statut', $projet->statut ?? 'planifie') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </x-select>
            <x-input-error :messages="$errors->get('statut')" />
        </div>
    </div>
</div>
