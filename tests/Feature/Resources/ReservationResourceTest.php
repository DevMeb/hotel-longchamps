<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\RenterResource;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\RoomResource;
use App\Models\Reservation;
use App\Models\Renter;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReservationResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_transforms_reservation_resource_correctly()
    {
        // Créer un locataire et une chambre
        $renter = Renter::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $room = Room::factory()->create([
            'name' => 'Room 101',
            'rent' => 50000, // Stocké en centimes
        ]);

        // Créer une réservation
        $reservation = Reservation::factory()->create([
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Transformer la réservation en ressource
        $resourceArray = (new ReservationResource($reservation))->toArray(request());

        // Construire l'array attendu
        $expectedArray = [
            'id' => $reservation->id,
            'renter' => (new RenterResource($renter))->toArray(request()),
            'room' => (new RoomResource($room))->toArray(request()),
            'start_date' => Carbon::parse('2024-08-01')->format('d/m/Y'),
            'end_date' => Carbon::parse('2024-08-10')->format('d/m/Y'),
            'created_at' => $reservation->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $reservation->updated_at->format('d/m/Y H:i:s'),
        ];

        // Vérifier que la ressource est transformée correctement
        $this->assertEquals($expectedArray, $resourceArray);
    }

    public function test_it_handles_null_end_date_correctly()
    {
        // Créer un locataire et une chambre
        $renter = Renter::factory()->create();

        $room = Room::factory()->create();

        // Créer une réservation sans date de fin (end_date)
        $reservation = Reservation::factory()->create([
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => '2024-08-01',
            'end_date' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Transformer la réservation en ressource
        $resource = new ReservationResource($reservation);

        // Convertir la ressource en tableau
        $resourceArray = $resource->toArray(request());

        // Construire l'array attendu
        $expectedArray = [
            'id' => $reservation->id,
            'renter' => (new RenterResource($renter))->toArray(request()),
            'room' => (new RoomResource($room))->toArray(request()),
            'start_date' => Carbon::parse('2024-08-01')->format('d/m/Y'),
            'end_date' => null,
            'created_at' => $reservation->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $reservation->updated_at->format('d/m/Y H:i:s'),
        ];

        // Vérifier que la ressource est transformée correctement, avec `end_date` nul
        $this->assertEquals($expectedArray, $resourceArray);
    }
}
