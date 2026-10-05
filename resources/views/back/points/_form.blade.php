{{-- Champs partagés entre la création et la modification d'un point de prélèvement --}}
<div class="space-y-5">
    <div>
        <x-input-label for="reseau_id" value="Réseau" />
        <x-select id="reseau_id" name="reseau_id" required>
            <option value="">Sélectionnez un réseau</option>
            @foreach ($reseaux as $reseau)
                <option value="{{ $reseau->id }}" @selected(old('reseau_id', $point->reseau_id) == $reseau->id)>
                    {{ $reseau->type }} — {{ $reseau->zone?->nom }}
                </option>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('reseau_id')" />
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="code" value="Code" />
            <x-text-input id="code" name="code" :value="old('code', $point->code)" placeholder="Ex: PP-TUN-012" class="uppercase placeholder:normal-case" required />
            <x-input-error :messages="$errors->get('code')" />
        </div>

        <div>
            <x-input-label for="type_point" value="Type de point" />
            <x-select id="type_point" name="type_point" required>
                <option value="">Sélectionnez un type</option>
                @foreach (\App\Models\PointPrelevement::TYPES as $key => $label)
                    <option value="{{ $key }}" @selected(old('type_point', $point->type_point) === $key)>{{ $label }}</option>
                @endforeach
            </x-select>
            <x-input-error :messages="$errors->get('type_point')" />
        </div>
    </div>

    <div>
        <x-input-label for="nom" value="Nom" />
        <x-text-input id="nom" name="nom" :value="old('nom', $point->nom)" placeholder="Ex: Fontaine de la Place" required />
        <x-input-error :messages="$errors->get('nom')" />
    </div>

    <div>
        <x-input-label for="adresse" value="Adresse" />
        <x-text-input id="adresse" name="adresse" :value="old('adresse', $point->adresse)" placeholder="Ex: Avenue Habib Bourguiba, Tunis" required />
        <x-input-error :messages="$errors->get('adresse')" />
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="latitude" value="Latitude (optionnelle)" />
            <x-text-input id="latitude" name="latitude" type="number" step="0.0000001" :value="old('latitude', $point->latitude)" placeholder="Ex: 36.8065" />
            <x-input-error :messages="$errors->get('latitude')" />
        </div>

        <div>
            <x-input-label for="longitude" value="Longitude (optionnelle)" />
            <x-text-input id="longitude" name="longitude" type="number" step="0.0000001" :value="old('longitude', $point->longitude)" placeholder="Ex: 10.1815" />
            <x-input-error :messages="$errors->get('longitude')" />
        </div>
    </div>
    <p class="text-xs text-muted -mt-3">Les coordonnées GPS permettent d'afficher le point sur une carte.</p>

    <label for="actif" class="flex items-center gap-3 cursor-pointer">
        <input type="hidden" name="actif" value="0">
        <input id="actif" name="actif" type="checkbox" value="1" @checked(old('actif', $point->actif ?? true))
               class="rounded border-border text-primary focus:ring-primary/30">
        <span class="text-sm text-ink">Point actif <span class="text-muted">(les points inactifs ne reçoivent plus d'analyses)</span></span>
    </label>
</div>
