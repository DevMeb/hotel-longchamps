<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'rent' => $this->faker->numberBetween(50000, 100000), // Rent en centimes
            // Ajoutez d'autres champs si nécessaire
        ];
    }
}
