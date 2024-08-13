<?php

namespace Tests\Feature\Services;

use App\Http\Services\RoomService;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $roomService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roomService = new RoomService();
    }

    public function test_it_can_get_all_rooms()
    {
        // Créer quelques chambres
        Room::factory()->count(3)->create();

        // Appeler le service
        $rooms = $this->roomService->getAllRooms();

        // Vérifier que toutes les chambres sont retournées
        $this->assertCount(3, $rooms);
    }

    public function test_it_can_create_a_room()
    {
        // Données de la chambre
        $data = [
            'name' => 'Room 101',
            'rent' => 500.00, // Loyer en euros
        ];

        // Appeler le service
        $room = $this->roomService->createRoom($data);

        // Vérifier que la chambre a été créée
        $this->assertInstanceOf(Room::class, $room);
        $this->assertDatabaseHas('rooms', [
            'name' => 'Room 101',
            'rent' => 50000, // Le loyer est stocké en centimes
        ]);
    }

    public function test_it_can_update_a_room()
    {
        // Créer une chambre
        $room = Room::factory()->create([
            'name' => 'Room 101',
            'rent' => 50000, // Stocké en centimes
        ]);

        // Nouvelles données
        $data = [
            'name' => 'Room 102',
            'rent' => 600.00, // Nouveau loyer en euros
        ];

        // Appeler le service
        $updatedRoom = $this->roomService->updateRoom($room, $data);

        // Vérifier que la chambre a été mise à jour
        $this->assertInstanceOf(Room::class, $updatedRoom);
        $this->assertEquals('Room 102', $updatedRoom->name);
        $this->assertEquals(60000, $updatedRoom->rent); // Vérifier le loyer en centimes
        $this->assertDatabaseHas('rooms', [
            'name' => 'Room 102',
            'rent' => 60000,
        ]);
    }

    public function test_it_can_delete_a_room()
    {
        // Créer une chambre
        $room = Room::factory()->create();

        // Appeler le service
        $this->roomService->deleteRoom($room);

        // Vérifier que la chambre a été supprimée
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    public function test_it_can_find_a_room_by_id()
    {
        // Créer une chambre
        $room = Room::factory()->create();

        // Appeler le service
        $foundRoom = $this->roomService->findRoomById($room->id);

        // Vérifier que la chambre a été trouvée
        $this->assertInstanceOf(Room::class, $foundRoom);
        $this->assertEquals($room->id, $foundRoom->id);
    }

    public function test_it_returns_null_if_room_not_found()
    {
        // Appeler le service avec un ID inexistant
        $foundRoom = $this->roomService->findRoomById(999);

        // Vérifier que la méthode retourne null
        $this->assertNull($foundRoom);
    }

    public function test_it_converts_euros_to_cents_when_creating_a_room()
    {
        // Données de la chambre avec un loyer en euros
        $data = [
            'name' => 'Room 101',
            'rent' => 450.75, // Loyer en euros
        ];

        // Appeler le service
        $room = $this->roomService->createRoom($data);

        // Vérifier que le loyer a été converti en centimes
        $this->assertEquals(45075, $room->rent);
        $this->assertDatabaseHas('rooms', [
            'name' => 'Room 101',
            'rent' => 45075, // Le loyer est stocké en centimes
        ]);
    }

    public function test_it_converts_euros_to_cents_when_updating_a_room()
    {
        // Créer une chambre
        $room = Room::factory()->create([
            'name' => 'Room 101',
            'rent' => 45075, // Stocké en centimes
        ]);

        // Nouvelles données avec un loyer en euros
        $data = [
            'rent' => 500.50, // Nouveau loyer en euros
        ];

        // Appeler le service
        $updatedRoom = $this->roomService->updateRoom($room, $data);

        // Vérifier que le loyer a été converti en centimes
        $this->assertEquals(50050, $updatedRoom->rent);
        $this->assertDatabaseHas('rooms', [
            'name' => 'Room 101',
            'rent' => 50050, // Le loyer est mis à jour en centimes
        ]);
    }
}
