<?php

namespace App\Http\Requests\Admin;

use App\Models\PointPrelevement;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AnalyseQualiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'point_id' => ['required', 'integer', 'exists:point_prelevements,id'],
            'date_prelevement' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:2000-01-01'],
            'ph' => ['required', 'numeric', 'between:0,14'],
            'chlore_residuel' => ['required', 'numeric', 'between:0,10'],
            'turbidite' => ['required', 'numeric', 'between:0,1000'],
            'nitrates' => ['required', 'numeric', 'between:0,500'],
            'bacteries_coliformes' => ['required', 'integer', 'between:0,100000'],
            'laboratoire' => ['required', 'string', 'min:3', 'max:150'],
        ];
    }

    /**
     * Règles métier :
     *  - une seule analyse par point et par jour ;
     *  - pas de nouvelle analyse sur un point désactivé.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $point = PointPrelevement::find($this->input('point_id'));
                $analyse = $this->route('analyse');
                $changementDePoint = ! $analyse || $analyse->point_id != $this->input('point_id');

                if ($point && $this->filled('date_prelevement') && ! $validator->errors()->has('date_prelevement')) {
                    $doublon = $point->analyses()
                        ->whereDate('date_prelevement', $this->date('date_prelevement'))
                        ->when($analyse, fn ($q) => $q->whereKeyNot($analyse->id))
                        ->exists();

                    if ($doublon) {
                        $validator->errors()->add('date_prelevement', 'Une analyse existe déjà pour ce point à cette date.');
                    }
                }

                if ($point && ! $point->actif && $changementDePoint && ! $validator->errors()->has('point_id')) {
                    $validator->errors()->add('point_id', 'Ce point de prélèvement est désactivé : aucune nouvelle analyse ne peut lui être ajoutée.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'point_id' => 'point de prélèvement',
            'date_prelevement' => 'date de prélèvement',
            'ph' => 'pH',
            'chlore_residuel' => 'chlore résiduel',
            'turbidite' => 'turbidité',
            'nitrates' => 'nitrates',
            'bacteries_coliformes' => 'coliformes',
            'laboratoire' => 'laboratoire',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'numeric' => 'Le champ :attribute doit être un nombre.',
            'integer' => 'Le champ :attribute doit être un nombre entier.',
            'between' => 'Le champ :attribute doit être compris entre :min et :max.',
            'exists' => 'Le :attribute sélectionné n\'existe pas.',
            'date' => 'Le champ :attribute doit être une date valide.',
            'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            'date_prelevement.before_or_equal' => 'La date de prélèvement ne peut pas être dans le futur.',
            'date_prelevement.after_or_equal' => 'La date de prélèvement est invalide.',
        ];
    }
}
