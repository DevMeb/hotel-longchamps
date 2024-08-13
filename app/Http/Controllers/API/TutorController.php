<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\TutorRequest;
use App\Http\Resources\TutorResource;
use App\Http\Services\TutorService;
use Illuminate\Http\JsonResponse;
use App\Models\Tutor;

class TutorController extends BaseController
{
    protected $tutorService;

    public function __construct(TutorService $tutorService)
    {
        $this->tutorService = $tutorService;
    }

    /**
     * Récupérer tous les tuteurs.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $tutors = $this->tutorService->getAllTutors();
            return $this->sendResponse(TutorResource::collection($tutors), 'Tuteurs récupérés avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération des tuteurs : ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Enregistrer un nouveau tuteur.
     *
     * @param TutorRequest $request
     * @return JsonResponse
     */
    public function store(TutorRequest $request): JsonResponse
    {
        try {
            $tutor = $this->tutorService->createTutor($request->validated());
            return $this->sendResponse(new TutorResource($tutor), 'Tuteur créé avec succès.', 201);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la création du tuteur : ' . $e->getMessage(), ['request' => $request->validated()], 500);
        }
    }

    /**
     * Afficher le tuteur spécifié.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $tutor = $this->tutorService->findTutorById($id);
            if (!$tutor) {
                return $this->sendError('Tuteur non trouvé.', ['id_tutor' => $id], 404);
            }
            return $this->sendResponse(new TutorResource($tutor), 'Tuteur récupéré avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération du tuteur : ' . $e->getMessage(), ['id_tutor' => $id], 500);
        }
    }

    /**
     * Mettre à jour le tuteur spécifié.
     *
     * @param TutorRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(TutorRequest $request, int $id): JsonResponse
    {
        try {
            $tutor = $this->tutorService->findTutorById($id);
            
            if (!$tutor) {
                return $this->sendError('Tuteur non trouvé.', ['id_tutor' => $id], 404);
            }

            $updatedTutor = $this->tutorService->updateTutor($tutor, $request->validated());
            return $this->sendResponse(new TutorResource($updatedTutor), 'Tuteur mis à jour avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la mise à jour du tuteur : ' . $e->getMessage(), ['request' => $request->validated()], 500);
        }
    }

    /**
     * Supprimer le tuteur spécifié.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $tutor = $this->tutorService->findTutorById($id);

            if (!$tutor) {
                return $this->sendError('Tuteur non trouvé.', [], 404);
            }

            $this->tutorService->deleteTutor($tutor);
            return $this->sendResponse(['tutor' => $tutor], 'Tuteur supprimé avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la suppression du tuteur : ' . $e->getMessage(), [], 500);
        }
    }
}