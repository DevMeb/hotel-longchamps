<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\RenterRequest;
use App\Http\Resources\RenterResource;
use App\Http\Services\RenterService;
use Illuminate\Http\JsonResponse;

class RenterController extends BaseController
{
    protected $renterService;

    public function __construct(RenterService $renterService)
    {
        $this->renterService = $renterService;
    }

    /**
     * Récupérer tous les locataires.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $renters = $this->renterService->getAllRenters();
            return $this->sendResponse(RenterResource::collection($renters), 'Locataires récupérés avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération des locataires : ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Enregistrer un nouveau locataire.
     *
     * @param RenterRequest $request
     * @return JsonResponse
     */
    public function store(RenterRequest $request): JsonResponse
    {
        try {
            $renter = $this->renterService->createRenter($request->validated());
            return $this->sendResponse(new RenterResource($renter), 'Locataire créé avec succès.', 201);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la création du locataire : ' . $e->getMessage(), ['request' => $request->validated()], 500);
        }
    }

    /**
     * Afficher le locataire spécifié.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $renter = $this->renterService->findRenterById($id);
            if (!$renter) {
                return $this->sendError('Locataire non trouvé.', ['id_renter' => $id], 404);
            }
            return $this->sendResponse(new RenterResource($renter), 'Locataire récupéré avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération du locataire : ' . $e->getMessage(), ['id_renter' => $id], 500);
        }
    }

    /**
     * Mettre à jour le locataire spécifié.
     *
     * @param RenterRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(RenterRequest $request, int $id): JsonResponse
    {
        try {
            $renter = $this->renterService->findRenterById($id);
            if (!$renter) {
                return $this->sendError('Locataire non trouvé.', ['id_renter' => $id], 404);
            }

            $updatedRenter = $this->renterService->updateRenter($renter, $request->validated());
            return $this->sendResponse(new RenterResource($updatedRenter), 'Locataire mis à jour avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la mise à jour du locataire : ' . $e->getMessage(), ['request' => $request->validated(), 'renter' => $renter], 500);
        }
    }

    /**
     * Supprimer le locataire spécifié.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $renter = $this->renterService->findRenterById($id);

            if (!$renter) {
                return $this->sendError('Locataire non trouvé.', [], 404);
            }

            $this->renterService->deleteRenter($renter);
            return $this->sendResponse(['renter' => $renter], 'Locataire supprimé avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la suppression du locataire : ' . $e->getMessage(), [], 500);
        }
    }
}
