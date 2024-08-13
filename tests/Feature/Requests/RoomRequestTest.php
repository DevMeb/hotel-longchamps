<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur et l'authentifier avec Sanctum
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_it_validates_required_fields()
    {
        // Faire une requête POST avec des données vides
        $response = $this->postJson('/api/rooms', []);

        // Vérifier que les champs 'name' et 'rent' sont requis
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'rent']);
        $response->assertJsonFragment(['name' => ['Le nom de la chambre est obligatoire.']]);
        $response->assertJsonFragment(['rent' => ['Le loyer est obligatoire.']]);
    }

    public function test_it_validates_name_is_a_string()
    {
        // Faire une requête POST avec un 'name' non valide
        $response = $this->postJson('/api/rooms', [
            'name' => 12345,
            'rent' => 500.00,
        ]);

        // Vérifier que le champ 'name' doit être une chaîne de caractères
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
        $response->assertJsonFragment(['name' => ['Le nom de la chambre doit être une chaîne de caractères.']]);
    }

    public function test_it_validates_name_max_length()
    {
        // Faire une requête POST avec un 'name' trop long
        $response = $this->postJson('/api/rooms', [
            'name' => str_repeat('a', 256),
            'rent' => 500.00,
        ]);

        // Vérifier que le champ 'name' ne dépasse pas 255 caractères
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
        $response->assertJsonFragment(['name' => ['Le nom de la chambre ne doit pas dépasser 255 caractères.']]);
    }

    public function test_it_validates_rent_is_numeric()
    {
        // Faire une requête POST avec un 'rent' non numérique
        $response = $this->postJson('/api/rooms', [
            'name' => 'Room 101',
            'rent' => 'not-a-number',
        ]);

        // Vérifier que le champ 'rent' doit être un nombre
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('rent');
        $response->assertJsonFragment(['rent' => ['Le loyer doit être un nombre.']]);
    }

    public function test_it_validates_rent_is_positive()
    {
        // Faire une requête POST avec un 'rent' négatif
        $response = $this->postJson('/api/rooms', [
            'name' => 'Room 101',
            'rent' => -100.00,
        ]);

        // Vérifier que le champ 'rent' doit être un nombre positif
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('rent');
        $response->assertJsonFragment(['rent' => ['Le loyer doit être un nombre positif.']]);
    }

    public function test_it_allows_valid_data()
    {
        // Faire une requête POST avec des données valides
        $response = $this->postJson('/api/rooms', [
            'name' => 'Room 101',
            'rent' => 500.00,
        ]);

        // Vérifier que les données valides sont acceptées
        $response->assertStatus(201); // Supposons que la création réussie retourne 201
        $response->assertJsonFragment([
            'name' => 'Room 101',
            'rent' => '500.00', // Vérifier le loyer sous forme de chaîne de caractères avec deux décimales
        ]);
    }

    public function test_it_blocks_unauthenticated_users()
    {
        // Ne pas authentifier un utilisateur pour ce test
        $this->app['auth']->forgetGuards();;

        // Faire une requête POST sans authentification
        $response = $this->postJson('/api/rooms', [
            'name' => 'Room 101',
            'rent' => 500.00,
        ]);

        // Vérifier que l'accès est refusé
        $response->assertStatus(401); // L'utilisateur non authentifié devrait recevoir un statut 401
    }
}

