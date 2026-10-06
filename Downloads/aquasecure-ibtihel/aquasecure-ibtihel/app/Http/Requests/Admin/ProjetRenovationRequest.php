<?php

namespace App\Http\Requests\Admin;

use App\Models\ProjetRenovation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjetRenovationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reseau_id' => ['required', 'integer', 'exists:reseau_eaus,id'],
            'titre' => ['required', 'string', 'min:3', 'max:255'],
            'statut' => ['required', 'string', Rule::in(array_keys(ProjetRenovation::STATUTS))],
        ];
    }

    public function attributes(): array
    {
        return [
            'reseau_id' => 'réseau',
            'titre' => 'titre du projet',
            'statut' => 'statut',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'in' => 'La valeur sélectionnée pour le :attribute est invalide.',
            'exists' => 'Le :attribute sélectionné n\'existe pas.',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
        ];
    }
}
