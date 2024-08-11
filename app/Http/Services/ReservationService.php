<?php

namespace App\Http\Services;

use App\Models\Reservation;

class ReservationService
{
    public function getAllReservations()
    {
        return Reservation::all();
    }

    public function createReservation(array $data): Reservation
    {
        return Reservation::create($data);
    }

    public function updateReservation(Reservation $reservation, array $data): Reservation
    {
        $reservation->update($data);
        return $reservation;
    }

    public function deleteReservation(Reservation $reservation): void
    {
        $reservation->delete();
    }

    public function findReservationById($id): ?Reservation
    {
        return Reservation::find($id);
    }
}
