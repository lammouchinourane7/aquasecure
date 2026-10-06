<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InterventionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'incident_id' => ['required', 'integer', 'exists:incidents,id'],
            'statut' => ['required', 'string', Rule::in(['planifiee', 'en_cours', 'terminee'])],
            'date_intervention' => ['required', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'incident_id' => 'incident',
            'statut' => 'statut',
            'date_intervention' => 'date d\'intervention',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'in' => 'La valeur choisie pour :attribute est invalide.',
            'exists' => 'L\':attribute sélectionné n\'existe pas.',
            'date' => 'Le champ :attribute doit être une date valide.',
        ];
    }
}
