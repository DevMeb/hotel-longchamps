<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reservation;

class ReservationRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette demande.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation qui s'appliquent à la requête.
     */
    public function rules(): array
    {
        return [
            'renter_id' => 'required|exists:renters,id',
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    /**
     * Messages d'erreur personnalisés pour la validation.
     */
    public function messages(): array
    {
        return [
            'renter_id.required' => 'Le locataire est requis.',
            'renter_id.exists' => 'Le locataire sélectionné n\'existe pas.',
            'room_id.required' => 'La chambre est requise.',
            'room_id.exists' => 'La chambre sélectionnée n\'existe pas.',
            'start_date.required' => 'La date de début est requise.',
            'start_date.date' => 'La date de début doit être une date valide.',
            'end_date.date' => 'La date de fin doit être une date valide.',
            'end_date.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'date_conflict' => 'Le locataire ou la chambre est déjà réservé(e) pour cette période.',
        ];
    }

    /**
     * Configurez le validateur pour ajouter la validation des conflits de réservation.
     *
     * Cette méthode est appelée après la validation standard pour ajouter une validation personnalisée,
     * vérifiant les conflits de dates pour les locataires et les chambres.
     */
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $renterId = $this->renter_id;
            $roomId = $this->room_id;
            $startDate = $this->start_date;
            $endDate = $this->end_date;
            $reservationId = $this->route('reservation') ? $this->route('reservation')->id : null;

            // Vérifier les conflits pour le locataire
            $renterConflict = Reservation::where('renter_id', $renterId)
                ->where('id', '!=', $reservationId) // Exclure la réservation courante
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $endDate ?? $startDate)
                            ->where(function ($query) use ($startDate) {
                                $query->where('end_date', '>=', $startDate)
                                        ->orWhereNull('end_date');
                            });
                    })
                    ->orWhere(function ($query) use ($startDate) {
                        $query->where('start_date', '>=', $startDate)
                            ->whereNull('end_date');
                    });
                })
                ->exists();

            // Vérifier les conflits pour la chambre
            $roomConflict = Reservation::where('room_id', $roomId)
                ->where('id', '!=', $reservationId) // Exclure la réservation courante
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $endDate ?? $startDate)
                            ->where(function ($query) use ($startDate) {
                                $query->where('end_date', '>=', $startDate)
                                        ->orWhereNull('end_date');
                            });
                    })
                    ->orWhere(function ($query) use ($startDate) {
                        $query->where('start_date', '>=', $startDate)
                            ->whereNull('end_date');
                    });
                })
                ->exists();

            if ($renterConflict || $roomConflict) {
                $validator->errors()->add('date_conflict', 'Le locataire ou la chambre est déjà réservé(e) pour cette période.');
            }
        });
    }

}
