<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReseauEauRequest extends FormRequest
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
            'zone_id' => ['required', 'exists:zones,id'],
            'type' => ['required', 'string', 'max:255'],
            'etat' => ['required', Rule::in(['bon', 'degrade', 'hors_service'])],
        ];
    }

    public function attributes(): array
    {
        return ['zone_id' => 'zone'];
    }
}
