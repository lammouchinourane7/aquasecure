<?php

namespace App\Http\Requests\Admin;

use App\Models\Capteur;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReleveRequest extends FormRequest
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
            'capteur_id' => ['required', 'integer', 'exists:capteurs,id'],
            'valeur' => ['required', 'numeric', 'between:-1000,100000'],
            'date_releve' => ['required', 'date', 'before_or_equal:now'],
            'remarque' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Règles métier liées au capteur choisi :
     *  - impossible d'enregistrer un relevé sur un capteur hors service ;
     *  - un relevé ne peut pas précéder l'installation du capteur.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $capteur = Capteur::find($this->input('capteur_id'));

                if (! $capteur) {
                    return;
                }

                if ($capteur->statut === 'hors_service' && ! $validator->errors()->has('capteur_id')) {
                    $validator->errors()->add('capteur_id', 'Ce capteur est hors service : aucun relevé ne peut lui être ajouté.');
                }

                $date = strtotime((string) $this->input('date_releve'));
                if ($date && $capteur->date_installation && $date < $capteur->date_installation->startOfDay()->getTimestamp()) {
                    $validator->errors()->add(
                        'date_releve',
                        'La date du relevé ne peut pas précéder l\'installation du capteur ('.$capteur->date_installation->format('d/m/Y').').'
                    );
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'capteur_id' => 'capteur',
            'valeur' => 'valeur',
            'date_releve' => 'date du relevé',
            'remarque' => 'remarque',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'numeric' => 'Le champ :attribute doit être un nombre.',
            'exists' => 'Le :attribute sélectionné n\'existe pas.',
            'date' => 'Le champ :attribute doit être une date valide.',
            'valeur.between' => 'La valeur doit être comprise entre :min et :max.',
            'date_releve.before_or_equal' => 'La date du relevé ne peut pas être dans le futur.',
            'remarque.max' => 'La remarque ne doit pas dépasser :max caractères.',
        ];
    }
}
