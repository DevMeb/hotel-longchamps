<?php

namespace Tests\Feature\Services;

use App\Http\Services\RegisterService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $registerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registerService = new RegisterService();
    }

    public function test_it_creates_a_user_with_hashed_password()
    {
        // Données simulées pour créer un utilisateur
        $userData = [
            'name' => 'Test User',
            'password' => 'password123',
        ];

        // Appeler la méthode createUser
        $user = $this->registerService->createUser($userData);

        // Vérifier que l'utilisateur a été créé
        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['name' => 'Test User']);

        // Vérifier que le mot de passe a été hashé
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_it_attempts_to_login_with_correct_credentials()
    {
        // Créer un utilisateur pour le test
        $user = User::factory()->create([
            'name' => 'Test User',
            'password' => Hash::make('password123'),
        ]);

        // Données d'identification correctes
        $credentials = ['name' => 'Test User', 'password' => 'password123'];

        // Appeler la méthode attemptLogin
        $result = $this->registerService->attemptLogin($credentials);

        // Vérifier que la tentative de connexion a réussi
        $this->assertTrue($result);
    }

    public function test_it_fails_login_with_incorrect_credentials()
    {
        // Créer un utilisateur pour le test
        $user = User::factory()->create([
            'name' => 'Test User',
            'password' => Hash::make('password123'),
        ]);

        // Données d'identification incorrectes
        $credentials = ['name' => 'Test User', 'password' => 'wrongpassword'];

        // Appeler la méthode attemptLogin
        $result = $this->registerService->attemptLogin($credentials);

        // Vérifier que la tentative de connexion a échoué
        $this->assertFalse($result);
    }

    public function test_it_prepares_user_data_with_token()
    {
        // Créer un utilisateur pour le test
        $user = User::factory()->create([
            'name' => 'Test User',
            'password' => Hash::make('password123'),
        ]);

        // Simuler la génération d'un token avec Sanctum
        Sanctum::actingAs($user);

        // Appeler la méthode prepareUserData
        $userData = $this->registerService->prepareUserData($user);

        // Vérifier que le tableau de données utilisateur contient le token et le nom
        $this->assertArrayHasKey('token', $userData);
        $this->assertArrayHasKey('name', $userData);
        $this->assertEquals('Test User', $userData['name']);
        $this->assertNotEmpty($userData['token']);
    }
}
