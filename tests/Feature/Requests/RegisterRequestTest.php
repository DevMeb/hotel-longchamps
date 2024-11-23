<?php

namespace Tests\Feature\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_it_allows_valid_data()
    {
        $data = [
            'name' => 'Valid User',
            'password' => 'password123',
            'c_password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201);  // Assuming successful creation returns 201
        $response->assertJson(['success' => true]);
    }

    public function test_it_fails_when_name_is_missing()
    {
        $data = [
            'password' => 'password123',
            'c_password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);  // Validation errors return a 422 status
        $response->assertJsonValidationErrors('name');
        $response->assertJsonFragment(['name' => ['Le nom est obligatoire.']]);
    }

    public function test_it_fails_when_password_is_missing()
    {
        $data = [
            'name' => 'Valid User',
            'c_password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
        $response->assertJsonFragment(['password' => ['Le mot de passe est obligatoire.']]);
    }

    public function test_it_fails_when_c_password_does_not_match()
    {
        $data = [
            'name' => 'Valid User',
            'password' => 'password123',
            'c_password' => 'differentpassword',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('c_password');
        $response->assertJsonFragment(['c_password' => ['La confirmation du mot de passe doit correspondre au mot de passe.']]);
    }

    public function test_it_fails_when_password_is_too_short()
    {
        $data = [
            'name' => 'Valid User',
            'password' => 'short',
            'c_password' => 'short',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
        $response->assertJsonFragment(['password' => ['Le mot de passe doit contenir au moins 8 caractères.']]);
    }

    public function test_it_fails_when_name_is_too_long()
    {
        $data = [
            'name' => str_repeat('a', 256),
            'password' => 'password123',
            'c_password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
        $response->assertJsonFragment(['name' => ['Le nom ne doit pas dépasser 255 caractères.']]);
    }
}
