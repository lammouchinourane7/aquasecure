<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reseau_id' => ['required', 'integer', 'exists:reseau_eaus,id'],
            'type' => ['required', 'string', Rule::in(['fuite', 'contamination', 'coupure'])],
            'gravite' => ['required', 'string', Rule::in(['faible', 'moyenne', 'critique'])],
            'date_signalement' => ['required', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'reseau_id' => 'réseau',
            'type' => 'type d\'incident',
            'gravite' => 'gravité',
            'date_signalement' => 'date de signalement',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'in' => 'La valeur choisie pour :attribute est invalide.',
            'exists' => 'Le :attribute sélectionné n\'existe pas.',
            'date' => 'Le champ :attribute doit être une date valide.',
        ];
    }
}
