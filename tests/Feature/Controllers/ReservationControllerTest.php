<?php

namespace Tests\Feature\Controllers;

use App\Models\Reservation;
use App\Models\Renter;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer et authentifier un utilisateur avec Sanctum
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_it_can_list_reservations()
    {
        // Créer des réservations
        Reservation::factory()->count(3)->create();

        // Faire une requête GET pour lister les réservations
        $response = $this->getJson('/api/reservations');

        // Vérifier le statut de la réponse
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_it_can_create_a_reservation()
    {
        // Créer un locataire et une chambre
        $renter = Renter::factory()->create();
        $room = Room::factory()->create();

        // Données de la réservation
        $data = [
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ];

        // Faire une requête POST pour créer une réservation
        $response = $this->postJson('/api/reservations', $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(201);

        // Vérifier le fragment JSON de la chambre
        $response->assertJsonFragment([
            'id' => $room->id,
            'name' => $room->name,
            'rent' => number_format($room->rent / 100, 2, '.', ' '), // Le loyer est en euros
        ]);

        // Vérifier le fragment JSON du locataire
        $response->assertJsonFragment([
            'id' => $renter->id,
            'first_name' => $renter->first_name,
            'last_name' => $renter->last_name,
        ]);

        // Vérifier que les données sont présentes dans la réponse
        $response->assertJsonFragment([
            'start_date' => '01/08/2024',
            'end_date' => '10/08/2024',
        ]);

        // Vérifier que les données sont enregistrées en base de données
        $this->assertDatabaseHas('reservations', $data);
    }

    public function test_it_can_show_a_reservation()
    {
        // Créer un locataire, une chambre et une réservation
        $renter = Renter::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        // Faire une requête GET pour afficher la réservation
        $response = $this->getJson("/api/reservations/{$reservation->id}");

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier le fragment JSON de la chambre
        $response->assertJsonFragment([
            'id' => $room->id,
            'name' => $room->name,
            'rent' => number_format($room->rent / 100, 2, '.', ' '), // Le loyer est en euros
        ]);

        // Vérifier le fragment JSON du locataire
        $response->assertJsonFragment([
            'id' => $renter->id,
            'first_name' => $renter->first_name,
            'last_name' => $renter->last_name,
        ]);

        // Vérifier les données retournées
        $response->assertJsonFragment([
            'id' => $reservation->id,
            'start_date' => '01/08/2024',
            'end_date' => '10/08/2024',
        ]);
    }

    public function test_it_returns_404_if_reservation_not_found()
    {
        // Faire une requête GET avec un ID inexistant
        $response = $this->getJson('/api/reservations/999');

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Réservation non trouvée.',
        ]);
    }

    public function test_it_can_update_a_reservation()
    {
        // Créer un locataire, une chambre et une réservation
        $renter = Renter::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        // Nouvelles données
        $data = [
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ];

        // Faire une requête PUT pour mettre à jour la réservation
        $response = $this->putJson("/api/reservations/{$reservation->id}", $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier les données mises à jour
        $response->assertJsonFragment([
            'start_date' => '05/08/2024',
            'end_date' => '15/08/2024',
        ]);

        // Vérifier que les données sont mises à jour en base de données
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ]);
    }

    public function test_it_can_delete_a_reservation()
    {
        // Créer une réservation
        $reservation = Reservation::factory()->create();

        // Faire une requête DELETE pour supprimer la réservation
        $response = $this->deleteJson("/api/reservations/{$reservation->id}");

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier que la réservation a été supprimée de la base de données
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    public function test_it_returns_404_if_reservation_not_found_when_updating()
    {
        // Créer un locataire, une chambre et une réservation
        $renter = Renter::factory()->create();
        $room = Room::factory()->create();

        // Données de mise à jour
        $data = [
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ];

        // Faire une requête PUT avec un ID inexistant
        $response = $this->putJson('/api/reservations/999', $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Réservation non trouvée.',
        ]);
    }

    public function test_it_returns_404_if_reservation_not_found_when_deleting()
    {
        // Faire une requête DELETE avec un ID inexistant
        $response = $this->deleteJson('/api/reservations/999');

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Réservation non trouvée.',
        ]);
    }
}
