<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Services\RegisterService;
use Illuminate\Support\Facades\Hash;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $registerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registerService = $this->app->make(RegisterService::class);
    }

    public function test_register_creates_a_new_user()
    {
        $data = [
            'name' => 'testuser',
            'password' => 'password',
            'c_password' => 'password',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Utilisateur enregistré avec succès.',
                 ]);

        $this->assertDatabaseHas('users', [
            'name' => 'testuser',
        ]);
    }

    public function test_register_returns_error_when_creation_fails()
    {
        // Simuler une erreur dans le service d'enregistrement
        $this->mock(RegisterService::class, function ($mock) {
            $mock->shouldReceive('createUser')->andThrow(new \Exception('Erreur lors de l\'enregistrement'));
        });

        $data = [
            'name' => 'testuser',
            'password' => 'password',
            'c_password' => 'password',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(500)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Une erreur est survenue lors de l\'enregistrement de l\'utilisateur.',
                 ]);
    }

    public function test_login_authenticates_a_user_successfully()
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'name' => 'testuser',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Connexion réussie.',
                ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_returns_error_for_invalid_credentials()
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'name' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Non autorisé.',
                ]);

        $this->assertGuest();
    }

}
