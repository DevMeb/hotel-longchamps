<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Invoice;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_id' => \App\Models\Reservation::factory(),
            'subject' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'billing_start_date' => Carbon::instance($this->faker->dateTimeThisMonth),
            'billing_end_date' => Carbon::instance($this->faker->dateTimeThisMonth),
            'issued_at' => null,
            'paid_at' => null,
            'status' => Invoice::STATUS_PENDING,
        ];
    }
}
