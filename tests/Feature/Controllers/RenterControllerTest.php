<?php

namespace Tests\Feature\Controllers;

use App\Models\Renter;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RenterControllerTest extends TestCase
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

    public function test_it_can_list_renters()
    {
        // Créer des locataires
        Renter::factory()->count(3)->create();

        // Faire une requête GET pour lister les locataires
        $response = $this->getJson('/api/renters');

        // Vérifier le statut de la réponse
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_it_can_create_a_renter()
    {
        // Créer un tuteur
        $tutor = Tutor::factory()->create();

        // Données du locataire
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'tutor_id' => $tutor->id,
        ];

        // Faire une requête POST pour créer un locataire
        $response = $this->postJson('/api/renters', $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(201);

        // Vérifier les fragments pertinents du JSON
        $response->assertJsonFragment([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        // Vérifier le fragment JSON du tuteur
        $response->assertJsonFragment([
            'first_name' => $tutor->first_name,
            'last_name' => $tutor->last_name,
            'email' => $tutor->email,
            'phone' => $tutor->phone,
        ]);

        // Vérifier que les données sont enregistrées en base de données
        $this->assertDatabaseHas('renters', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'tutor_id' => $tutor->id,
        ]);
    }

    public function test_it_can_show_a_renter()
    {
        // Créer un locataire
        $renter = Renter::factory()->create();

        // Faire une requête GET pour afficher le locataire
        $response = $this->getJson("/api/renters/{$renter->id}");

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier les données retournées
        $response->assertJsonFragment([
            'first_name' => $renter->first_name,
            'last_name' => $renter->last_name,
        ]);
    }

    public function test_it_returns_404_if_renter_not_found()
    {
        // Faire une requête GET avec un ID inexistant
        $response = $this->getJson('/api/renters/999');

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Locataire non trouvé.',
        ]);
    }

    public function test_it_can_update_a_renter()
    {
        // Créer un locataire
        $renter = Renter::factory()->create();

        // Nouvelles données
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ];

        // Faire une requête PUT pour mettre à jour le locataire
        $response = $this->putJson("/api/renters/{$renter->id}", $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier les données mises à jour
        $response->assertJsonFragment([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        // Vérifier que les données sont mises à jour en base de données
        $this->assertDatabaseHas('renters', [
            'id' => $renter->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
    }

    public function test_it_can_delete_a_renter()
    {
        // Créer un locataire
        $renter = Renter::factory()->create();

        // Faire une requête DELETE pour supprimer le locataire
        $response = $this->deleteJson("/api/renters/{$renter->id}");

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier que le locataire a été supprimé de la base de données
        $this->assertDatabaseMissing('renters', ['id' => $renter->id]);
    }

    public function test_it_returns_404_if_renter_not_found_when_updating()
    {
        // Données de mise à jour
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ];

        // Faire une requête PUT avec un ID inexistant
        $response = $this->putJson('/api/renters/999', $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Locataire non trouvé.',
        ]);
    }

    public function test_it_returns_404_if_renter_not_found_when_deleting()
    {
        // Faire une requête DELETE avec un ID inexistant
        $response = $this->deleteJson('/api/renters/999');

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Locataire non trouvé.',
        ]);
    }
}
