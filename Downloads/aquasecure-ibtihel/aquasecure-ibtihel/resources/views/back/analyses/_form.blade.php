{{-- Champs partagés entre la création et la modification d'une analyse --}}
@use('App\Models\AnalyseQualite')

@php
    $champs = [
        'ph' => ['step' => '0.01', 'placeholder' => 'Ex: 7.4'],
        'chlore_residuel' => ['step' => '0.01', 'placeholder' => 'Ex: 0.5'],
        'turbidite' => ['step' => '0.01', 'placeholder' => 'Ex: 1.2'],
        'nitrates' => ['step' => '0.01', 'placeholder' => 'Ex: 18'],
        'bacteries_coliformes' => ['step' => '1', 'placeholder' => 'Ex: 0'],
    ];
    $valeursInitiales = collect($champs)->mapWithKeys(fn ($c, $p) => [$p => (string) old($p, $analyse->{$p})]);
@endphp

<div class="space-y-5"
     x-data="{
        normes: @js(AnalyseQualite::NORMES),
        v: @js($valeursInitiales),
        etat(p) {
            const val = this.v[p];
            if (val === '' || val === null || isNaN(val)) return null;
            const n = this.normes[p], x = parseFloat(val);
            return (n.min === null || x >= n.min) && (n.max === null || x <= n.max);
        },
        get verdict() {
            const etats = Object.keys(this.normes).map(p => this.etat(p));
            if (etats.includes(null)) return null;
            return !etats.includes(false);
        }
     }">

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="point_id" value="Point de prélèvement" />
            <x-select id="point_id" name="point_id" required>
                <option value="">Sélectionnez un point</option>
                @foreach ($points as $reseau => $groupe)
                    <optgroup label="{{ $reseau }}">
                        @foreach ($groupe as $point)
                            <option value="{{ $point->id }}"
                                    @selected(old('point_id', $analyse->point_id) == $point->id)
                                    @disabled(! $point->actif && $analyse->point_id != $point->id)>
                                {{ $point->code }} — {{ $point->nom }}{{ $point->actif ? '' : ' · inactif' }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </x-select>
            <x-input-error :messages="$errors->get('point_id')" />
        </div>

        <div>
            <x-input-label for="date_prelevement" value="Date de prélèvement" />
            <x-text-input id="date_prelevement" name="date_prelevement" type="date" max="{{ now()->toDateString() }}"
                          :value="old('date_prelevement', $analyse->date_prelevement?->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('date_prelevement')" />
        </div>
    </div>

    <div class="rounded-xl border border-border p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="font-display text-sm font-bold text-ink">Résultats de l'analyse</p>
            <template x-if="verdict === true"><x-badge color="success">Eau conforme</x-badge></template>
            <template x-if="verdict === false"><x-badge color="alert">Eau non conforme</x-badge></template>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            @foreach ($champs as $param => $champ)
                @php $norme = AnalyseQualite::NORMES[$param]; @endphp
                <div>
                    <x-input-label :for="$param" :value="$norme['label'].($norme['unite'] ? ' ('.$norme['unite'].')' : '')" />
                    <x-text-input :id="$param" :name="$param" type="number" :step="$champ['step']" min="0"
                                  x-model="v.{{ $param }}" :value="old($param, $analyse->{$param})" :placeholder="$champ['placeholder']" required />
                    <p class="text-xs mt-1">
                        <span class="text-muted">Norme : {{ AnalyseQualite::normeTexte($param) }}</span>
                        <span x-show="etat('{{ $param }}') === true" style="display: none;" class="ml-1 font-semibold text-success-strong">· OK</span>
                        <span x-show="etat('{{ $param }}') === false" style="display: none;" class="ml-1 font-semibold text-alert-strong">· hors norme</span>
                    </p>
                    <x-input-error :messages="$errors->get($param)" />
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <x-input-label for="laboratoire" value="Laboratoire" />
        <x-text-input id="laboratoire" name="laboratoire" list="laboratoires" :value="old('laboratoire', $analyse->laboratoire)" placeholder="Ex: Institut Pasteur de Tunis" required />
        <datalist id="laboratoires">
            @foreach (['Laboratoire central SONEDE', 'Institut Pasteur de Tunis', 'Laboratoire régional d\'hygiène', 'Laboratoire ONAS'] as $labo)
                <option value="{{ $labo }}">
            @endforeach
        </datalist>
        <x-input-error :messages="$errors->get('laboratoire')" />
    </div>
</div>
