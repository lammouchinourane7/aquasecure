<?php

namespace App\Http\Requests\Admin;

use App\Models\Capteur;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CapteurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise le code saisi (majuscules, sans espaces) avant validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->input('code')))]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $capteur = $this->route('capteur');

        return [
            'reseau_id' => ['required', 'integer', 'exists:reseau_eaus,id'],
            'code' => [
                'required', 'string', 'max:50', 'regex:/^[A-Z0-9]+(-[A-Z0-9]+)*$/',
                Rule::unique('capteurs', 'code')->ignore($capteur?->id),
            ],
            'modele' => ['required', 'string', 'min:2', 'max:100'],
            'type_mesure' => ['required', Rule::in(array_keys(Capteur::TYPES))],
            'unite' => ['required', Rule::in(array_column(Capteur::TYPES, 'unite'))],
            'seuil_min' => ['required', 'numeric'],
            'seuil_max' => ['required', 'numeric', 'gt:seuil_min'],
            'date_installation' => ['required', 'date', 'before_or_equal:today'],
            'statut' => ['required', Rule::in(array_keys(Capteur::STATUTS))],
        ];
    }

    /**
     * Règle métier : l'unité doit correspondre au type de mesure
     * (ex. une pression se mesure en bar, pas en m3/h).
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $type = $this->input('type_mesure');
                $unite = $this->input('unite');

                if (isset(Capteur::TYPES[$type]) && $unite && Capteur::TYPES[$type]['unite'] !== $unite) {
                    $validator->errors()->add(
                        'unite',
                        "L'unité d'un capteur de type ".mb_strtolower(Capteur::TYPES[$type]['label']).' doit être « '.Capteur::TYPES[$type]['unite'].' ».'
                    );
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'reseau_id' => 'réseau',
            'code' => 'code',
            'modele' => 'modèle',
            'type_mesure' => 'type de mesure',
            'unite' => 'unité',
            'seuil_min' => 'seuil minimum',
            'seuil_max' => 'seuil maximum',
            'date_installation' => "date d'installation",
            'statut' => 'statut',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'numeric' => 'Le champ :attribute doit être un nombre.',
            'in' => 'La valeur choisie pour :attribute est invalide.',
            'exists' => 'Le :attribute sélectionné n\'existe pas.',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
            'date' => 'Le champ :attribute doit être une date valide.',
            'code.regex' => 'Le code doit contenir uniquement des lettres majuscules, des chiffres et des tirets (ex. CAP-PRE-001).',
            'code.unique' => 'Ce code est déjà utilisé par un autre capteur.',
            'seuil_max.gt' => 'Le seuil maximum doit être strictement supérieur au seuil minimum.',
            'date_installation.before_or_equal' => "La date d'installation ne peut pas être dans le futur.",
        ];
    }
}
