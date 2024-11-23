<?php

use Tests\TestCase;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Renter;
use App\Models\Room;
use App\Models\User;

class ReservationRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $renter1;
    protected $renter2;

    protected $room1;
    protected $room2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Création de quelques locataires et chambres pour les tests
        $this->renter1 = Renter::factory()->create();
        $this->renter2 = Renter::factory()->create();
        $this->room1 = Room::factory()->create();
        $this->room2 = Room::factory()->create();
    }

    public function test_it_detects_conflict_when_renter_has_another_reservation_for_same_period()
    {
        $this->actingAs($this->user, 'sanctum');

        Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        $response = $this->post('/api/reservations', [
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room2->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ]);

        $response->assertSessionHasErrors('renter_conflict');
    }

    public function test_it_detects_conflict_when_room_is_already_reserved_for_same_period()
    {
        $this->actingAs($this->user, 'sanctum');

        Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        $response = $this->post('/api/reservations', [
            'renter_id' => $this->renter2->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ]);

        $response->assertSessionHasErrors('room_conflict');
    }

    public function test_it_detects_conflict_when_both_renter_and_room_have_conflicts_for_same_period()
    {
        $this->actingAs($this->user, 'sanctum');

        Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        $response = $this->post('/api/reservations', [
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ]);

        $response->assertSessionHasErrors('date_conflict');
    }

    public function test_it_detects_conflict_when_renter_has_long_term_reservation_during_period()
    {
        $this->actingAs($this->user, 'sanctum');

        Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => null, // Long-term reservation
        ]);

        $response = $this->post('/api/reservations', [
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room2->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ]);

        $response->assertSessionHasErrors('renter_conflict');
    }

    public function test_it_detects_conflict_when_room_has_long_term_reservation_during_period()
    {
        $this->actingAs($this->user, 'sanctum');

        Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => null, // Long-term reservation
        ]);

        $response = $this->post('/api/reservations', [
            'renter_id' => $this->renter2->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-05',
            'end_date' => '2024-08-15',
        ]);

        $response->assertSessionHasErrors('room_conflict');
    }

    public function test_it_allows_reservation_when_there_is_no_conflict()
    {
        $this->actingAs($this->user, 'sanctum');

        Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        $response = $this->post('/api/reservations', [
            'renter_id' => $this->renter2->id,
            'room_id' => $this->room2->id,
            'start_date' => '2024-08-11',
            'end_date' => '2024-08-15',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_it_allows_reservation_when_renter_and_room_have_different_periods()
    {
        $this->actingAs($this->user, 'sanctum');

        Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room2->id,
            'start_date' => '2024-08-11',
            'end_date' => '2024-08-15',
        ]);

        $response = $this->post('/api/reservations', [
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-11',
            'end_date' => '2024-08-15',
        ]);

        $response->assertSessionHasErrors('renter_conflict');
    }

    public function test_it_blocks_unauthenticated_users_from_creating_reservations()
    {
        // Suppression de l'authentification pour tester les utilisateurs non authentifiés
        $response = $this->post('/api/reservations', [
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-11',
            'end_date' => '2024-08-15',
        ]);

        // S'assurer que la réponse est une redirection ou un statut 401
        if ($response->status() === 302) {
            $response->assertRedirect(); // Assure que c'est une redirection vers une page de login
        } else {
            $response->assertStatus(401); // Pour une API, s'attend à un 401
        }
    }

    public function test_it_detects_conflict_when_updating_reservation_to_conflicting_period()
    {
        $this->actingAs($this->user, 'sanctum');

        // Création de deux réservations distinctes
        $reservation1 = Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        $reservation2 = Reservation::create([
            'renter_id' => $this->renter2->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-11',
            'end_date' => '2024-08-15',
        ]);

        // Tentative de mise à jour de la première réservation pour chevaucher la deuxième
        $response = $this->put('/api/reservations/' . $reservation1->id, [
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-09',
            'end_date' => '2024-08-12',
        ]);

        $response->assertSessionHasErrors('room_conflict');
    }

    public function test_it_allows_updating_reservation_when_no_conflict_exists()
    {
        $this->actingAs($this->user, 'sanctum');

        // Création d'une réservation
        $reservation = Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        // Mise à jour de la réservation avec une période différente sans conflit
        $response = $this->put('/api/reservations/' . $reservation->id, [
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-15',
            'end_date' => '2024-08-20',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_it_detects_conflict_when_updating_reservation_to_overlap_with_another_reservation_of_same_renter()
    {
        $this->actingAs($this->user, 'sanctum');

        // Création de deux réservations distinctes pour le même locataire
        $reservation1 = Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        $reservation2 = Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room2->id,
            'start_date' => '2024-08-15',
            'end_date' => '2024-08-20',
        ]);

        // Tentative de mise à jour de la première réservation pour chevaucher la deuxième
        $response = $this->put('/api/reservations/' . $reservation1->id, [
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-18',
            'end_date' => '2024-08-22',
        ]);

        $response->assertSessionHasErrors('renter_conflict');
    }

    public function test_it_allows_updating_reservation_when_no_overlap_with_same_renter_reservation()
    {
        $this->actingAs($this->user, 'sanctum');

        // Création de deux réservations distinctes pour le même locataire
        $reservation1 = Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-08-10',
        ]);

        $reservation2 = Reservation::create([
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room2->id,
            'start_date' => '2024-08-15',
            'end_date' => '2024-08-20',
        ]);

        // Mise à jour de la première réservation avec une période qui ne chevauche pas la deuxième
        $response = $this->put('/api/reservations/' . $reservation1->id, [
            'renter_id' => $this->renter1->id,
            'room_id' => $this->room1->id,
            'start_date' => '2024-08-11',
            'end_date' => '2024-08-14',
        ]);

        $response->assertSessionHasNoErrors();
    }
}
