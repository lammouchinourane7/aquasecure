<?php

namespace App\Http\Requests\Admin;

use App\Models\Financement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FinancementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'projet_id' => ['required', 'integer', 'exists:projet_renovations,id'],
            'source' => ['required', 'string', Rule::in(array_keys(Financement::SOURCES))],
            'montant' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
        ];
    }

    public function attributes(): array
    {
        return [
            'projet_id' => 'projet de rénovation',
            'source' => 'source de financement',
            'montant' => 'montant',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'numeric' => 'Le champ :attribute doit être une valeur numérique.',
            'min' => 'Le :attribute doit être supérieur à zéro.',
            'max' => 'Le :attribute ne peut pas dépasser 99 999 999,99.',
            'in' => 'La :attribute sélectionnée est invalide.',
            'exists' => 'Le :attribute sélectionné n\'existe pas.',
        ];
    }
}
