<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    /**
     * Créer un nouvel utilisateur
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    /**
     * Tenter de se connecter
     */
    public function attemptLogin(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    /**
     * Préparer les données utilisateur pour la réponse
     */
    public function prepareUserData(User $user): array
    {
        return [
            'token' => $user->createToken('MyApp')->plainTextToken,
            'name' => $user->name,
        ];
    }
}
