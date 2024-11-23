<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_transforms_room_resource_correctly()
    {
        // Créer une chambre avec un loyer en centimes
        $room = Room::factory()->create([
            'name' => 'Room 101',
            'rent' => 50000, // 500,00 € en centimes
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Transformer la chambre en ressource
        $resource = new RoomResource($room);

        // Convertir la ressource en tableau
        $resourceArray = $resource->toArray(request());

        // Construire l'array attendu
        $expectedArray = [
            'id' => $room->id,
            'name' => 'Room 101',
            'rent' => '500.00', // Le loyer devrait être en euros avec deux décimales
            'created_at' => $room->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $room->updated_at->format('d/m/Y H:i:s'),
        ];

        // Vérifier que la ressource est transformée correctement
        $this->assertEquals($expectedArray, $resourceArray);
    }
}
