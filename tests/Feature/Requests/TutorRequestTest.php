<?php

namespace Tests\Feature\Requests;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class TutorRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur pour l'authentification
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);
    }

    public function test_it_allows_valid_data()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(201); // Supposons que la création réussie retourne 201
        $response->assertJson(['success' => true]);
    }

    public function test_it_fails_when_first_name_is_missing()
    {
        $data = [
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('first_name');
        $response->assertJsonFragment(['first_name' => ['Le prénom est obligatoire.']]);
    }

    public function test_it_fails_when_first_name_is_not_a_string()
    {
        $data = [
            'first_name' => 12345, // Type de données invalide
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('first_name');
        $responseContent = $response->json('errors.first_name');
        
        $this->assertContains('Le prénom doit être une chaîne de caractères.', $responseContent);
        $this->assertContains('Le prénom ne doit contenir que des lettres, des espaces, ou des tirets.', $responseContent);
    }


    public function test_it_fails_when_first_name_exceeds_max_length()
    {
        $data = [
            'first_name' => str_repeat('a', 256), // Dépasse 255 caractères
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('first_name');
        $response->assertJsonFragment(['first_name' => ['Le prénom ne doit pas dépasser 255 caractères.']]);
    }

    public function test_it_fails_when_first_name_contains_invalid_characters()
    {
        $data = [
            'first_name' => 'John123', // Contient des chiffres
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('first_name');
        $response->assertJsonFragment(['first_name' => ['Le prénom ne doit contenir que des lettres, des espaces, ou des tirets.']]);
    }

    public function test_it_fails_when_last_name_is_missing()
    {
        $data = [
            'first_name' => 'John',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
        $response->assertJsonFragment(['last_name' => ['Le nom de famille est obligatoire.']]);
    }

    public function test_it_fails_when_last_name_is_not_a_string()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 12345, // Type de données invalide
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrors('last_name');
        $responseContent = $response->json('errors.last_name');
        
        $this->assertContains('Le nom de famille doit être une chaîne de caractères.', $responseContent);
        $this->assertContains('Le nom de famille ne doit contenir que des lettres, des espaces, ou des tirets.', $responseContent);
    }

    public function test_it_fails_when_last_name_exceeds_max_length()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => str_repeat('a', 256), // Dépasse 255 caractères
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
        $response->assertJsonFragment(['last_name' => ['Le nom de famille ne doit pas dépasser 255 caractères.']]);
    }

    public function test_it_fails_when_last_name_contains_invalid_characters()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe123', // Contient des chiffres
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
        $response->assertJsonFragment(['last_name' => ['Le nom de famille ne doit contenir que des lettres, des espaces, ou des tirets.']]);
    }

    public function test_it_fails_when_email_is_missing()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $response->assertJsonFragment(['email' => ['L\'adresse email est obligatoire.']]);
    }

    public function test_it_fails_when_email_is_invalid()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $response->assertJsonFragment(['email' => ['L\'adresse email doit être une adresse email valide.']]);
    }

    public function test_it_fails_when_email_is_not_unique()
    {
        Tutor::factory()->create(['email' => 'john.doe@example.com']);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $response->assertJsonFragment(['email' => ['L\'adresse email est déjà utilisée. Veuillez en choisir une autre.']]);
    }

    public function test_it_fails_when_phone_is_missing()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('phone');
        $response->assertJsonFragment(['phone' => ['Le numéro de téléphone est obligatoire.']]);
    }

    public function test_it_fails_when_phone_is_invalid()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => 'invalid-phone',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('phone');
        $response->assertJsonFragment(['phone' => ['Le numéro de téléphone ne doit contenir que des chiffres.']]);
    }

    public function test_it_fails_when_phone_exceeds_max_length()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => str_repeat('1', 21), // Dépasse 20 caractères
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('phone');
        $response->assertJsonFragment(['phone' => ['Le numéro de téléphone ne doit pas dépasser 20 caractères.']]);
    }

    public function test_it_blocks_unauthenticated_users()
    {
        // Ne pas authentifier un utilisateur pour ce test
        $this->app['auth']->forgetGuards();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        $response = $this->postJson('/api/tutors', $data);

        $response->assertStatus(401); // L'utilisateur non authentifié devrait recevoir un statut 401
    }
}
