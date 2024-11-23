<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\RegisterRequest;
use App\Http\Services\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class RegisterController extends BaseController
{
    protected $registerService;

    // Injecter le service d'enregistrement via le constructeur
    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * Inscrire un nouvel utilisateur
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->registerService->createUser($request->all());
            return $this->sendResponse($this->registerService->prepareUserData($user), 'Utilisateur enregistré avec succès.', JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->sendError('Une erreur est survenue lors de l\'enregistrement de l\'utilisateur.', [$e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Connexion d'un utilisateur existant
     */
    public function login(Request $request): JsonResponse
    {
        if ($this->registerService->attemptLogin($request->only('name', 'password'))) {
            $user = Auth::user();
            return $this->sendResponse($this->registerService->prepareUserData($user), 'Connexion réussie.');
        } else {
            return $this->sendError('Non autorisé.', ['error' => 'Non autorisé'], JsonResponse::HTTP_UNAUTHORIZED);
        }
    }
}