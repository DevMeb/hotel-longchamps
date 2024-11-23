<?php

namespace Tests\Feature\Controllers;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TutorControllerTest extends TestCase
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

    public function test_it_can_list_tutors()
    {
        // Créer des tuteurs
        Tutor::factory()->count(3)->create();

        // Faire une requête GET pour lister les tuteurs
        $response = $this->getJson('/api/tutors');

        // Vérifier le statut de la réponse
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_it_can_create_a_tutor()
    {
        // Données du tuteur
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        // Faire une requête POST pour créer un tuteur
        $response = $this->postJson('/api/tutors', $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(201);

        // Vérifier que les données sont présentes dans la réponse
        $response->assertJsonFragment([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ]);

        // Vérifier que les données sont enregistrées en base de données
        $this->assertDatabaseHas('tutors', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ]);
    }

    public function test_it_can_show_a_tutor()
    {
        // Créer un tuteur
        $tutor = Tutor::factory()->create();

        // Faire une requête GET pour afficher le tuteur
        $response = $this->getJson("/api/tutors/{$tutor->id}");

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier les données retournées
        $response->assertJsonFragment([
            'first_name' => $tutor->first_name,
            'last_name' => $tutor->last_name,
            'email' => $tutor->email,
            'phone' => $tutor->phone,
        ]);
    }

    public function test_it_returns_404_if_tutor_not_found()
    {
        // Faire une requête GET avec un ID inexistant
        $response = $this->getJson('/api/tutors/999');

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Tuteur non trouvé.',
        ]);
    }

    public function test_it_can_update_a_tutor()
    {
        // Créer un tuteur
        $tutor = Tutor::factory()->create();

        // Nouvelles données
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '0987654321',
        ];

        // Faire une requête PUT pour mettre à jour le tuteur
        $response = $this->putJson("/api/tutors/{$tutor->id}", $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier les données mises à jour
        $response->assertJsonFragment([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '0987654321',
        ]);

        // Vérifier que les données sont mises à jour en base de données
        $this->assertDatabaseHas('tutors', [
            'id' => $tutor->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '0987654321',
        ]);
    }

    public function test_it_can_delete_a_tutor()
    {
        // Créer un tuteur
        $tutor = Tutor::factory()->create();

        // Faire une requête DELETE pour supprimer le tuteur
        $response = $this->deleteJson("/api/tutors/{$tutor->id}");

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier que le tuteur a été supprimé de la base de données
        $this->assertDatabaseMissing('tutors', ['id' => $tutor->id]);
    }

    public function test_it_returns_404_if_tutor_not_found_when_updating()
    {
        // Données de mise à jour
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '0987654321',
        ];

        // Faire une requête PUT avec un ID inexistant
        $response = $this->putJson('/api/tutors/999', $data);

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Tuteur non trouvé.',
        ]);
    }

    public function test_it_returns_404_if_tutor_not_found_when_deleting()
    {
        // Faire une requête DELETE avec un ID inexistant
        $response = $this->deleteJson('/api/tutors/999');

        // Vérifier le statut de la réponse
        $response->assertStatus(404);

        // Vérifier le message d'erreur
        $response->assertJsonFragment([
            'message' => 'Tuteur non trouvé.',
        ]);
    }
}
