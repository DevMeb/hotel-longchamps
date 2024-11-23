<?php

namespace Tests\Feature\Requests;

use App\Models\Tutor;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RenterRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
        $response = $this->postJson('/api/renters', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['first_name', 'last_name']);
        $response->assertJsonFragment(['first_name' => ['Le prénom est obligatoire.']]);
        $response->assertJsonFragment(['last_name' => ['Le nom de famille est obligatoire.']]);
    }

    public function test_it_validates_first_name_is_a_string()
    {
        $response = $this->postJson('/api/renters', [
            'first_name' => 12345,
            'last_name' => 'Doe',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('first_name');
        $responseContent = $response->json('errors.first_name');
        
        $this->assertContains('Le prénom doit être une chaîne de caractères.', $responseContent);
        $this->assertContains('Le prénom ne doit contenir que des lettres, des espaces, ou des tirets.', $responseContent);
    }

    public function test_it_validates_first_name_max_length()
    {
        $response = $this->postJson('/api/renters', [
            'first_name' => str_repeat('a', 256),
            'last_name' => 'Doe',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('first_name');
        $response->assertJsonFragment(['first_name' => ['Le prénom ne doit pas dépasser 255 caractères.']]);
    }

    public function test_it_validates_first_name_regex()
    {
        $response = $this->postJson('/api/renters', [
            'first_name' => 'John123!',
            'last_name' => 'Doe',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('first_name');
        $response->assertJsonFragment(['first_name' => ['Le prénom ne doit contenir que des lettres, des espaces, ou des tirets.']]);
    }

    public function test_it_validates_last_name_is_a_string()
    {
        $response = $this->postJson('/api/renters', [
            'first_name' => 'John',
            'last_name' => 12345,
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('last_name');
        $responseContent = $response->json('errors.last_name');
        
        $this->assertContains('Le nom de famille doit être une chaîne de caractères.', $responseContent);
        $this->assertContains('Le nom de famille ne doit contenir que des lettres, des espaces, ou des tirets.', $responseContent);
    }

    public function test_it_validates_last_name_max_length()
    {
        $response = $this->postJson('/api/renters', [
            'first_name' => 'John',
            'last_name' => str_repeat('a', 256),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
        $response->assertJsonFragment(['last_name' => ['Le nom de famille ne doit pas dépasser 255 caractères.']]);
    }

    public function test_it_validates_last_name_regex()
    {
        $response = $this->postJson('/api/renters', [
            'first_name' => 'John',
            'last_name' => 'Doe123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
        $response->assertJsonFragment(['last_name' => ['Le nom de famille ne doit contenir que des lettres, des espaces, ou des tirets.']]);
    }

    public function test_it_validates_tutor_id_exists_if_provided()
    {
        $response = $this->postJson('/api/renters', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'tutor_id' => 999, // ID inexistant
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('tutor_id');
        $response->assertJsonFragment(['tutor_id' => ['Le tuteur sélectionné n\'existe pas.']]);
    }

    public function test_it_allows_valid_data()
    {
        // Créer un tuteur
        $tutor = Tutor::factory()->create();

        $response = $this->postJson('/api/renters', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'tutor_id' => $tutor->id,
        ]);

        // Vérifier le statut de la réponse
        $response->assertStatus(201); // Supposons que la création réussie retourne 201

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
    }
}