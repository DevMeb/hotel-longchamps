<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Renter;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        return [
            'renter_id' => Renter::factory(),  // Crée automatiquement un Renter associé
            'room_id' => Room::factory(),      // Crée automatiquement une Room associée
            'start_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+2 months')->format('Y-m-d'),
        ];
    }
}

