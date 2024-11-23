<?php

namespace Tests\Feature\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoomControllerTest extends TestCase
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

    public function test_it_can_list_rooms()
    {
        // Créer des chambres
        Room::factory()->count(3)->create();

        // Faire une requête GET pour lister les chambres
        $response = $this->getJson('/api/rooms');

        // Vérifier le statut de la réponse
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_it_can_create_a_room()
    {
        // Données de la chambre
        $data = [
            'name' => 'Room 101',
            'rent' => 500.00,
        ];

        // Faire une requête POST pour créer une chambre
        $response = $this->postJson('/api/rooms', $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(201);

        // Vérifier que les données sont présentes dans la réponse
        $response->assertJsonFragment([
            'name' => 'Room 101',
            'rent' => '500.00', // Le loyer est en euros avec deux décimales
        ]);

        // Vérifier que les données sont enregistrées en base de données
        $this->assertDatabaseHas('rooms', [
            'name' => 'Room 101',
            'rent' => 50000, // Le loyer est stocké en centimes
        ]);
    }

    public function test_it_can_show_a_room()
    {
        // Créer une chambre
        $room = Room::factory()->create([
            'name' => 'Room 101',
            'rent' => 50000, // Stocké en centimes
        ]);

        // Faire une requête GET pour afficher la chambre
        $response = $this->getJson("/api/rooms/{$room->id}");

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier les données retournées
        $response->assertJsonFragment([
            'name' => 'Room 101',
            'rent' => '500.00', // Le loyer est en euros avec deux décimales
        ]);
    }

    public function test_it_returns_404_if_room_not_found()
    {
        // Faire une requête GET avec un ID inexistant
        $response = $this->getJson('/api/rooms/999');

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Chambre non trouvée.',
        ]);
    }

    public function test_it_can_update_a_room()
    {
        // Créer une chambre
        $room = Room::factory()->create([
            'name' => 'Room 101',
            'rent' => 50000, // Stocké en centimes
        ]);

        // Nouvelles données
        $data = [
            'name' => 'Room 102',
            'rent' => 600.00, // Nouveau loyer en euros
        ];

        // Faire une requête PUT pour mettre à jour la chambre
        $response = $this->putJson("/api/rooms/{$room->id}", $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier les données mises à jour
        $response->assertJsonFragment([
            'name' => 'Room 102',
            'rent' => '600.00', // Le loyer est en euros avec deux décimales
        ]);

        // Vérifier que les données sont mises à jour en base de données
        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'name' => 'Room 102',
            'rent' => 60000, // Le loyer est mis à jour en centimes
        ]);
    }

    public function test_it_can_delete_a_room()
    {
        // Créer une chambre
        $room = Room::factory()->create();

        // Faire une requête DELETE pour supprimer la chambre
        $response = $this->deleteJson("/api/rooms/{$room->id}");

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier que la chambre a été supprimée de la base de données
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    public function test_it_returns_404_if_room_not_found_when_updating()
    {
        // Données de mise à jour
        $data = [
            'name' => 'Room 102',
            'rent' => 600.00, // Nouveau loyer en euros
        ];

        // Faire une requête PUT avec un ID inexistant
        $response = $this->putJson('/api/rooms/999', $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Chambre non trouvée.',
        ]);
    }

    public function test_it_returns_404_if_room_not_found_when_deleting()
    {
        // Faire une requête DELETE avec un ID inexistant
        $response = $this->deleteJson('/api/rooms/999');

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Chambre non trouvée.',
        ]);
    }
}
