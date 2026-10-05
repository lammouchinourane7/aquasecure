@use('App\Models\Financement')

<div class="space-y-5">
    <div>
        <x-input-label for="projet_id" value="Projet de rénovation concerné" />
        <x-select id="projet_id" name="projet_id" required>
            <option value="">Sélectionnez un projet de rénovation</option>
            @foreach ($projets as $p)
                <option value="{{ $p->id }}" @selected(old('projet_id', $financement->projet_id) == $p->id)>
                    {{ $p->titre }} &mdash; Réseau : {{ $p->reseauEau?->type }}
                </option>
            @endforeach
        </x-select>
        <x-input-error :messages="$errors->get('projet_id')" />
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="source" value="Source de financement" />
            <x-select id="source" name="source" required>
                @foreach (Financement::SOURCES as $key => $label)
                    <option value="{{ $key }}" @selected(old('source', $financement->source ?? 'Etat') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </x-select>
            <x-input-error :messages="$errors->get('source')" />
        </div>

        <div>
            <x-input-label for="montant" value="Montant alloué (en TND)" />
            <div class="relative">
                <x-text-input id="montant" name="montant" type="number" step="0.01" min="0.01" max="99999999.99"
                              :value="old('montant', $financement->montant)"
                              placeholder="Ex : 150000.00"
                              class="!pr-14"
                              required />
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-muted">TND</span>
            </div>
            <x-input-error :messages="$errors->get('montant')" />
        </div>
    </div>
</div>
