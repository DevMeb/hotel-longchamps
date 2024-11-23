<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Http\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use App\Models\Reservation;

class ReservationController extends BaseController
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Récupérer toutes les réservations.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $reservations = $this->reservationService->getAllReservations();
            return $this->sendResponse(ReservationResource::collection($reservations), 'Réservations récupérées avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération des réservations : ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Enregistrer une nouvelle réservation.
     *
     * @param ReservationRequest $request
     * @return JsonResponse
     */
    public function store(ReservationRequest $request): JsonResponse
    {
        try {
            $reservation = $this->reservationService->createReservation($request->validated());
            return $this->sendResponse(new ReservationResource($reservation), 'Réservation créée avec succès.', 201);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la création de la réservation : ' . $e->getMessage(), ['request' => $request->validated()], 500);
        }
    }

    /**
     * Afficher la réservation spécifiée.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $reservation = $this->reservationService->findReservationById($id);
            if (!$reservation) {
                return $this->sendError('Réservation non trouvée.', ['id_reservation' => $id], 404);
            }
            return $this->sendResponse(new ReservationResource($reservation), 'Réservation récupérée avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération de la réservation : ' . $e->getMessage(), ['id_reservation' => $id], 500);
        }
    }

    /**
     * Mettre à jour la réservation spécifiée.
     *
     * @param ReservationRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ReservationRequest $request, int $id): JsonResponse
    {
        try {
            $reservation = $this->reservationService->findReservationById($id);
            
            if (!$reservation) {
                return $this->sendError('Réservation non trouvée.', ['id_reservation' => $id], 404);
            }

            $updatedReservation = $this->reservationService->updateReservation($reservation, $request->validated());
            return $this->sendResponse(new ReservationResource($updatedReservation), 'Réservation mise à jour avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la mise à jour de la réservation : ' . $e->getMessage(), ['request' => $request->validated()], 500);
        }
    }

    /**
     * Supprimer la réservation spécifiée.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $reservation = $this->reservationService->findReservationById($id);

            if (!$reservation) {
                return $this->sendError('Réservation non trouvée.', [], 404);
            }

            $this->reservationService->deleteReservation($reservation);
            return $this->sendResponse(['reservation' => $reservation], 'Réservation supprimée avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la suppression de la réservation : ' . $e->getMessage(), [], 500);
        }
    }
}
