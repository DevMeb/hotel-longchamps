<?php

namespace Tests\Feature\Services;

use App\Http\Services\ReservationService;
use App\Models\Renter;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $reservationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reservationService = new ReservationService();
    }

    public function test_it_can_get_all_reservations()
    {
        // Créer quelques réservations
        Reservation::factory()->count(3)->create();

        // Appeler le service
        $reservations = $this->reservationService->getAllReservations();

        // Vérifier que toutes les réservations sont retournées
        $this->assertCount(3, $reservations);
    }

    public function test_it_can_create_a_reservation()
    {

        $renter = Renter::factory()->create();
        $room = Room::factory()->create();

        // Données de la réservation
        $data = [
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ];

        // Appeler le service
        $reservation = $this->reservationService->createReservation($data);

        // Vérifier que la réservation a été créée
        $this->assertInstanceOf(Reservation::class, $reservation);
        $this->assertDatabaseHas('reservations', $data);
    }

    public function test_it_can_update_a_reservation()
    {

        $renter = Renter::factory()->create();
        $room = Room::factory()->create();

        // Créer une réservation
        $reservation = Reservation::factory()->create([
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        // Nouvelles données
        $data = [
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ];

        // Appeler le service
        $updatedReservation = $this->reservationService->updateReservation($reservation, $data);

        // Vérifier que la réservation a été mise à jour
        $this->assertInstanceOf(Reservation::class, $updatedReservation);
        $this->assertEquals('2024-08-05', $updatedReservation->start_date);
        $this->assertEquals('2024-08-15', $updatedReservation->end_date);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ]);
    }

    public function test_it_can_delete_a_reservation()
    {
        // Créer une réservation
        $reservation = Reservation::factory()->create();

        // Appeler le service
        $this->reservationService->deleteReservation($reservation);

        // Vérifier que la réservation a été supprimée
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    public function test_it_can_find_a_reservation_by_id()
    {
        // Créer une réservation
        $reservation = Reservation::factory()->create();

        // Appeler le service
        $foundReservation = $this->reservationService->findReservationById($reservation->id);

        // Vérifier que la réservation a été trouvée
        $this->assertInstanceOf(Reservation::class, $foundReservation);
        $this->assertEquals($reservation->id, $foundReservation->id);
    }

    public function test_it_returns_null_if_reservation_not_found()
    {
        // Appeler le service avec un ID inexistant
        $foundReservation = $this->reservationService->findReservationById(999);

        // Vérifier que la méthode retourne null
        $this->assertNull($foundReservation);
    }
}
