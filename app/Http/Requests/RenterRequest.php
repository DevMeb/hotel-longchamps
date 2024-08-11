<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RenterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\-]+$/u' // Autorise seulement les lettres, espaces, et tirets
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\-]+$/u' // Autorise seulement les lettres, espaces, et tirets
            ],
            'tutor_id' => [
                'nullable',
                'exists:tutors,id' // Vérifie si le tuteur existe dans la table tutors
            ],
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.string' => 'Le prénom doit être une chaîne de caractères.',
            'first_name.max' => 'Le prénom ne doit pas dépasser 255 caractères.',
            'first_name.regex' => 'Le prénom ne doit contenir que des lettres, des espaces, ou des tirets.',

            'last_name.required' => 'Le nom de famille est obligatoire.',
            'last_name.string' => 'Le nom de famille doit être une chaîne de caractères.',
            'last_name.max' => 'Le nom de famille ne doit pas dépasser 255 caractères.',
            'last_name.regex' => 'Le nom de famille ne doit contenir que des lettres, des espaces, ou des tirets.',

            'tutor_id.exists' => 'Le tuteur sélectionné n\'existe pas.',
        ];
    }
}