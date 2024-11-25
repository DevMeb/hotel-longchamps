<?php

namespace Database\Factories;

use App\Models\Renter;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Factories\Factory;

class RenterFactory extends Factory
{
    protected $model = Renter::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'tutor_id' => Tutor::factory(), // Génère automatiquement un tutor si aucun n'est passé
        ];
    }
}
