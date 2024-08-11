<?php

namespace App\Http\Services;

use App\Models\Room;

class RoomService
{
    public function getAllRooms()
    {
        return Room::all();
    }

    public function createRoom(array $data): Room
    {
        $data['rent'] = $this->convertEurosToCents($data['rent']); // Convertir le loyer en centimes
        return Room::create($data);
    }

    public function updateRoom(Room $room, array $data): Room
    {
        if (isset($data['rent'])) {
            $data['rent'] = $this->convertEurosToCents($data['rent']); // Convertir le loyer en centimes
        }
        $room->update($data);
        return $room;
    }

    public function deleteRoom(Room $room): void
    {
        $room->delete();
    }

    public function findRoomById($id): ?Room
    {
        return Room::find($id);
    }

    private function convertEurosToCents($amount): int
    {
        return (int)($amount * 100); // Convertir en centimes
    }
}
