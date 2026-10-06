{{-- Champs partagés entre la création et la modification d'un capteur --}}
@php
    $types = \App\Models\Capteur::TYPES;
    $unites = collect($types)->mapWithKeys(fn ($t, $k) => [$k => $t['unite']]);
@endphp

<div class="space-y-5"
     x-data="{ type: @js(old('type_mesure', $capteur->type_mesure)), unite: @js(old('unite', $capteur->unite)), unites: @js($unites) }">

    <div>
        <x-input-label for="reseau_id" value="Réseau équipé" />
        <x-select id="reseau_id" name="reseau_id" required>
            <option value="">Sélectionnez un réseau</option>
            @foreach ($reseaux as $reseau)
                <option value="{{ $reseau->id }}" @selected(old('reseau_id', $capteur->reseau_id) == $reseau->id)>
                    {{ $reseau->type }} — {{ $reseau->zone?->nom }}
                </option>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('reseau_id')" />
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="code" value="Code" />
            <x-text-input id="code" name="code" :value="old('code', $capteur->code)" placeholder="Ex: CAP-PRE-001" class="uppercase placeholder:normal-case" required />
            <x-input-error :messages="$errors->get('code')" />
        </div>

        <div>
            <x-input-label for="modele" value="Modèle" />
            <x-text-input id="modele" name="modele" :value="old('modele', $capteur->modele)" placeholder="Ex: Siemens SITRANS P" required />
            <x-input-error :messages="$errors->get('modele')" />
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="type_mesure" value="Type de mesure" />
            <x-select id="type_mesure" name="type_mesure" x-model="type" x-on:change="unite = unites[type] ?? ''" required>
                <option value="">Sélectionnez un type</option>
                @foreach ($types as $key => $t)
                    <option value="{{ $key }}" @selected(old('type_mesure', $capteur->type_mesure) === $key)>{{ $t['label'] }}</option>
                @endforeach
            </x-select>
            <x-input-error :messages="$errors->get('type_mesure')" />
        </div>

        <div>
            <x-input-label for="unite" value="Unité" />
            <x-select id="unite" name="unite" x-model="unite" required>
                <option value="">—</option>
                @foreach ($unites->unique() as $u)
                    <option value="{{ $u }}" @selected(old('unite', $capteur->unite) === $u)>{{ $u }}</option>
                @endforeach
            </x-select>
            <p class="text-xs text-muted mt-1">Remplie automatiquement selon le type de mesure.</p>
            <x-input-error :messages="$errors->get('unite')" />
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="seuil_min" value="Seuil minimum" />
            <x-text-input id="seuil_min" name="seuil_min" type="number" step="0.01" :value="old('seuil_min', $capteur->seuil_min)" required />
            <x-input-error :messages="$errors->get('seuil_min')" />
        </div>

        <div>
            <x-input-label for="seuil_max" value="Seuil maximum" />
            <x-text-input id="seuil_max" name="seuil_max" type="number" step="0.01" :value="old('seuil_max', $capteur->seuil_max)" required />
            <x-input-error :messages="$errors->get('seuil_max')" />
        </div>
    </div>
    <p class="text-xs text-muted -mt-3">Tout relevé en dehors de cette plage sera automatiquement signalé comme anomalie.</p>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="date_installation" value="Date d'installation" />
            <x-text-input id="date_installation" name="date_installation" type="date" max="{{ now()->toDateString() }}"
                          :value="old('date_installation', $capteur->date_installation?->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('date_installation')" />
        </div>

        <div>
            <x-input-label for="statut" value="Statut" />
            <x-select id="statut" name="statut" required>
                @foreach (\App\Models\Capteur::STATUTS as $key => $label)
                    <option value="{{ $key }}" @selected(old('statut', $capteur->statut) === $key)>{{ $label }}</option>
                @endforeach
            </x-select>
            <x-input-error :messages="$errors->get('statut')" />
        </div>
    </div>
</div>
