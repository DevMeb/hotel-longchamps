<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Http\Services\RoomService;
use Illuminate\Http\JsonResponse;
use App\Models\Room;

class RoomController extends BaseController
{
    protected $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function index(): JsonResponse
    {
        try {
            $rooms = $this->roomService->getAllRooms();
            return $this->sendResponse(RoomResource::collection($rooms), 'Chambres récupérées avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération des chambres : ' . $e->getMessage(), [], 500);
        }
    }

    public function store(RoomRequest $request): JsonResponse
    {
        try {
            $room = $this->roomService->createRoom($request->validated());
            return $this->sendResponse(new RoomResource($room), 'Chambre créée avec succès.', 201);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la création de la chambre : ' . $e->getMessage(), ['request' => $request->validated()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $room = $this->roomService->findRoomById($id);
            if (!$room) {
                return $this->sendError('Chambre non trouvée.', ['id_room' => $id], 404);
            }
            return $this->sendResponse(new RoomResource($room), 'Chambre récupérée avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération de la chambre : ' . $e->getMessage(), ['id_room' => $id], 500);
        }
    }

    public function update(RoomRequest $request, Room $room): JsonResponse
    {
        try {
            $updatedRoom = $this->roomService->updateRoom($room, $request->validated());
            return $this->sendResponse(new RoomResource($updatedRoom), 'Chambre mise à jour avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la mise à jour de la chambre : ' . $e->getMessage(), ['request' => $request->validated(), 'room' => $room], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $room = $this->roomService->findRoomById($id);

            if (!$room) {
                return $this->sendError('Chambre non trouvée.', [], 404);
            }

            $this->roomService->deleteRoom($room);
            return $this->sendResponse(['room' => $room], 'Chambre supprimée avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la suppression de la chambre : ' . $e->getMessage(), [], 500);
        }
    }
}
