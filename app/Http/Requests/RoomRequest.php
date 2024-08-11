<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'rent' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la chambre est obligatoire.',
            'name.string' => 'Le nom de la chambre doit être une chaîne de caractères.',
            'name.max' => 'Le nom de la chambre ne doit pas dépasser 255 caractères.',
            'rent.required' => 'Le loyer est obligatoire.',
            'rent.numeric' => 'Le loyer doit être un nombre.',
            'rent.min' => 'Le loyer doit être un nombre positif.',
        ];
    }
}

