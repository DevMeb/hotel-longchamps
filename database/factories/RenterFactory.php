<?php

namespace Database\Factories;

use App\Models\Renter;
use Illuminate\Database\Eloquent\Factories\Factory;

class RenterFactory extends Factory
{
    protected $model = Renter::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            // Ajoutez d'autres champs si nécessaire
        ];
    }
}
