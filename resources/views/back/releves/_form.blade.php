{{-- Champs partagés entre la création et la modification d'un relevé --}}
@php
    // Infos des capteurs pour l'aide à la saisie (plage normale affichée en direct)
    $infosCapteurs = $capteurs->flatten()->mapWithKeys(fn ($c) => [$c->id => [
        'unite' => $c->unite, 'min' => $c->seuil_min, 'max' => $c->seuil_max,
    ]]);
@endphp

<div class="space-y-5"
     x-data="{
        capteurId: @js((string) old('capteur_id', $releve->capteur_id)),
        valeur: @js((string) old('valeur', $releve->valeur)),
        infos: @js($infosCapteurs),
        get info() { return this.infos[this.capteurId] ?? null },
        get horsSeuil() {
            if (!this.info || this.valeur === '' || isNaN(this.valeur)) return null;
            const v = parseFloat(this.valeur);
            return v < this.info.min || v > this.info.max;
        }
     }">

    <div>
        <x-input-label for="capteur_id" value="Capteur" />
        <x-select id="capteur_id" name="capteur_id" x-model="capteurId" required>
            <option value="">Sélectionnez un capteur</option>
            @foreach ($capteurs as $reseau => $groupe)
                <optgroup label="{{ $reseau }}">
                    @foreach ($groupe as $capteur)
                        <option value="{{ $capteur->id }}"
                                @selected(old('capteur_id', $releve->capteur_id) == $capteur->id)
                                @disabled($capteur->statut === 'hors_service' && $releve->capteur_id != $capteur->id)>
                            {{ $capteur->code }} — {{ $capteur->type_label }} ({{ $capteur->unite }}){{ $capteur->statut === 'hors_service' ? ' · hors service' : '' }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('capteur_id')" />
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="valeur" value="Valeur mesurée" />
            <div class="relative">
                <x-text-input id="valeur" name="valeur" type="number" step="0.01" x-model="valeur"
                              :value="old('valeur', $releve->valeur)" class="pr-16" required />
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-muted" x-text="info?.unite ?? ''"></span>
            </div>
            <p class="text-xs mt-1" x-show="info" style="display: none;">
                <span class="text-muted">Plage normale : <span x-text="info?.min"></span> – <span x-text="info?.max"></span></span>
                <template x-if="horsSeuil === true"><span class="ml-1 font-semibold text-alert-strong">· hors seuil</span></template>
                <template x-if="horsSeuil === false"><span class="ml-1 font-semibold text-success-strong">· normal</span></template>
            </p>
            <x-input-error :messages="$errors->get('valeur')" />
        </div>

        <div>
            <x-input-label for="date_releve" value="Date et heure du relevé" />
            <x-text-input id="date_releve" name="date_releve" type="datetime-local" max="{{ now()->format('Y-m-d\TH:i') }}"
                          :value="old('date_releve', $releve->date_releve?->format('Y-m-d\TH:i'))" required />
            <x-input-error :messages="$errors->get('date_releve')" />
        </div>
    </div>

    <div>
        <x-input-label for="remarque" value="Remarque (optionnelle)" />
        <x-textarea id="remarque" name="remarque" rows="3" maxlength="255" placeholder="Ex: valeur vérifiée sur site">{{ old('remarque', $releve->remarque) }}</x-textarea>
        <x-input-error :messages="$errors->get('remarque')" />
    </div>
</div>
