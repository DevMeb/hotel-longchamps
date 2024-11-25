<?php

namespace Database\Seeders;

use App\Models\Renter;
use App\Models\Tutor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer 10 tutors
        Tutor::factory()
            ->count(10)
            ->has(Renter::factory()->count(5)) // Chaque tutor aura 5 renters
            ->create();
    }
}
