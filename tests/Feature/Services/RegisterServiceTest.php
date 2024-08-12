<?php
namespace Tests\Feature\Services;

use App\Http\Services\RegisterService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Mockery;

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

        // Mock de Auth::attempt pour simuler une tentative de connexion réussie
        Auth::shouldReceive('attempt')
            ->with($credentials)
            ->andReturn(true);

        // Appeler la méthode attemptLogin
        $result = $this->registerService->attemptLogin($credentials);

        // Vérifier que la tentative de connexion a réussi
        $this->assertTrue($result);
    }
}
