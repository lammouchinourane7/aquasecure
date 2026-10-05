<?php

namespace App\Http\Requests\Admin;

use App\Models\PointPrelevement;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PointPrelevementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise le code et la case à cocher « actif » avant validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->input('code'))),
            'actif' => $this->boolean('actif'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $point = $this->route('point');

        return [
            'reseau_id' => ['required', 'integer', 'exists:reseau_eaus,id'],
            'code' => [
                'required', 'string', 'max:50', 'regex:/^PP-[A-Z0-9]+(-[A-Z0-9]+)*$/',
                Rule::unique('point_prelevements', 'code')->ignore($point?->id),
            ],
            'nom' => ['required', 'string', 'min:3', 'max:150'],
            'adresse' => ['required', 'string', 'min:5', 'max:255'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'type_point' => ['required', Rule::in(array_keys(PointPrelevement::TYPES))],
            'actif' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'reseau_id' => 'réseau',
            'code' => 'code',
            'nom' => 'nom',
            'adresse' => 'adresse',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'type_point' => 'type de point',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'numeric' => 'Le champ :attribute doit être un nombre.',
            'in' => 'La valeur choisie pour :attribute est invalide.',
            'exists' => 'Le :attribute sélectionné n\'existe pas.',
            'min' => 'Le champ :attribute doit contenir au moins :min caractères.',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            'between' => 'La :attribute doit être comprise entre :min et :max.',
            'required_with' => 'Renseignez à la fois la latitude et la longitude (ou aucune des deux).',
            'code.regex' => 'Le code doit commencer par « PP- » et ne contenir que des majuscules, chiffres et tirets (ex. PP-TUN-012).',
            'code.unique' => 'Ce code est déjà utilisé par un autre point de prélèvement.',
        ];
    }
}
