<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
            'reservation_id' => [
                'required',
                'exists:reservations,id', // Vérifie que l'ID de la réservation existe dans la table 'reservations'
            ],
            'subject' => [
                'required',
                'string',
                'max:255',
            ],
            'billing_start_date' => [
                'required',
                'date', // Assure que c'est une date valide
                'before_or_equal:billing_end_date', // La date de début doit être avant ou égale à la date de fin
            ],
            'billing_end_date' => [
                'required',
                'date', // Assure que c'est une date valide
                'after_or_equal:billing_start_date', // La date de fin doit être après ou égale à la date de début
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'issued_at' => [
                'nullable',
                'date', // Assure que c'est une date valide
            ],
            'paid_at' => [
                'nullable',
                'date', // Assure que c'est une date valide
                'after_or_equal:issued_at', // La date de paiement doit être après ou égale à la date d'émission
            ],
            'status' => [
                'required',
                'in:pending,issued,paid', // Assure que le statut est l'une des valeurs prédéfinies
            ],
        ];
    }

    public function messages()
    {
        return [
            'reservation_id.required' => 'L\'ID de la réservation est obligatoire.',
            'reservation_id.exists' => 'La réservation spécifiée n\'existe pas.',

            'subject.required' => 'Le sujet est obligatoire.',
            'subject.string' => 'Le sujet doit être une chaîne de caractères.',
            'subject.max' => 'Le sujet ne doit pas dépasser 255 caractères.',

            'billing_start_date.required' => 'La date de début de facturation est obligatoire.',
            'billing_start_date.date' => 'La date de début de facturation doit être une date valide.',
            'billing_start_date.before_or_equal' => 'La date de début de facturation doit être antérieure ou égale à la date de fin de facturation.',

            'billing_end_date.required' => 'La date de fin de facturation est obligatoire.',
            'billing_end_date.date' => 'La date de fin de facturation doit être une date valide.',
            'billing_end_date.after_or_equal' => 'La date de fin de facturation doit être postérieure ou égale à la date de début de facturation.',

            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',

            'issued_at.date' => 'La date d\'émission doit être une date valide.',

            'paid_at.date' => 'La date de paiement doit être une date valide.',
            'paid_at.after_or_equal' => 'La date de paiement doit être postérieure ou égale à la date d\'émission.',

            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être l\'une des valeurs suivantes : pending, issued, paid.',
        ];
    }
}
