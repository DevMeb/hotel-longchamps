<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\Renter;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User; // Assurez-vous d'importer le modèle User ou celui que vous utilisez pour l'authentification
use Laravel\Sanctum\Sanctum;

class ReservationRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $renter;
    protected $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renter = Renter::factory()->create();
        $this->room = Room::factory()->create();

        // Créez un utilisateur et authentifiez-le
        $user = User::factory()->create();
        Sanctum::actingAs($user); // Utilisez Sanctum si vous utilisez Sanctum pour l'authentification
    }

    public function test_it_detects_conflict_when_new_reservation_overlaps_existing_without_end_date(): void
    {
        // Création de la première réservation sans date de fin
        Reservation::create([
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-10',
            'end_date' => null,
        ]);

        // Tentative de création d'une nouvelle réservation qui commence après
        $response = $this->postJson('/api/reservations', [
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-12',
            'end_date' => null,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('date_conflict');
    }

    public function test_it_detects_conflict_when_new_reservation_starts_before_existing_without_end_date(): void
    {
        // Création de la première réservation sans date de fin
        Reservation::create([
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-10',
            'end_date' => null,
        ]);

        // Tentative de création d'une nouvelle réservation qui commence avant
        $response = $this->postJson('/api/reservations', [
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-08',
            'end_date' => null,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('date_conflict');
    }

    public function test_it_allows_non_conflicting_reservation(): void
    {
        // Création de la première réservation avec une date de fin
        Reservation::create([
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-10',
            'end_date' => '2024-08-15',
        ]);

        // Tentative de création d'une nouvelle réservation qui ne chevauche pas
        $response = $this->postJson('/api/reservations', [
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-16',
            'end_date' => '2024-08-20',
        ]);

        $response->assertStatus(201)
                 ->assertJsonMissingValidationErrors('date_conflict');
    }

    public function test_it_detects_conflict_when_modifying_existing_reservation(): void
    {
        // Création d'une première réservation
        $reservation = Reservation::create([
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-10',
            'end_date' => '2024-08-15',
        ]);

        // Création d'une deuxième réservation
        Reservation::create([
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-20',
            'end_date' => null,
        ]);

        // Tentative de modification de la première réservation qui cause un conflit
        $response = $this->putJson('/api/reservations/' . $reservation->id, [
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-12',
            'end_date' => null,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('date_conflict');
    }

    public function test_it_detects_conflict_with_existing_reservation_when_creating_new_one(): void
    {
        // Création de la première réservation avec date de fin
        Reservation::create([
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        // Tentative de créer une nouvelle réservation qui chevauche la première
        $response = $this->postJson('/api/reservations', [
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('date_conflict');
    }

    public function test_it_allows_reservation_with_no_conflicts_after_modification(): void
    {
        // Création d'une première réservation
        $reservation = Reservation::create([
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-10',
            'end_date' => '2024-08-15',
        ]);

        // Création d'une deuxième réservation
        Reservation::create([
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-20',
            'end_date' => '2024-08-25',
        ]);

        // Modification de la première réservation sans conflit
        $response = $this->putJson('/api/reservations/' . $reservation->id, [
            'renter_id' => $this->renter->id,
            'room_id' => $this->room->id,
            'start_date' => '2024-08-16',
            'end_date' => '2024-08-19',
        ]);

        $response->assertStatus(200)
                 ->assertJsonMissingValidationErrors('date_conflict');
    }
}
