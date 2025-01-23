<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class TutorRequest extends FormRequest
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
        $tutorId = $this->route('tutor');

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
            'email' => [
                'required',
                'email',
                $tutorId ? 'unique:tutors,email,' . $tutorId : 'unique:tutors,email'
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d+$/' // Autorise seulement les chiffres
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

            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être une adresse email valide.',
            'email.unique' => 'L\'adresse email est déjà utilisée. Veuillez en choisir une autre.',

            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
            'phone.regex' => 'Le numéro de téléphone ne doit contenir que des chiffres.',
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new ValidationException($validator, response()->json([
            'message' => 'Des erreurs de validation ont eu lieu pour les tuteurs.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
