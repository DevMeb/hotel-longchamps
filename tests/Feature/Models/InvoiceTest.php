<?php

namespace Tests\Models;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Renter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_mark_invoice_as_pending()
    {
        $invoice = Invoice::factory()->create();

        $invoice->markAsPending();

        $this->assertEquals(Invoice::STATUS_PENDING, $invoice->status);
    }

    public function test_it_can_mark_invoice_as_issued()
    {
        $invoice = Invoice::factory()->create();

        $invoice->markAsIssued();

        $this->assertEquals(Invoice::STATUS_ISSUED, $invoice->status);
        $this->assertNotNull($invoice->issued_at);
    }

    public function test_it_can_mark_invoice_as_paid()
    {
        $invoice = Invoice::factory()->create();

        $invoice->markAsPaid();

        $this->assertEquals(Invoice::STATUS_PAID, $invoice->status);
        $this->assertNotNull($invoice->paid_at);
    }

    public function test_it_generates_correct_description()
    {
        $renter = Renter::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $room = Room::factory()->create([
            'name' => 'Room 101',
            'rent' => 500,
        ]);

        $reservation = Reservation::factory()->create([
            'renter_id' => $renter->id,
            'room_id' => $room->id,
        ]);

        $invoice = Invoice::factory()->create([
            'reservation_id' => $reservation->id,
            'subject' => 'Loyer de septembre',
            'billing_start_date' => now()->startOfMonth(),
            'billing_end_date' => now()->endOfMonth(),
        ]);

        $description = $invoice->description;

        $expectedDescription = sprintf(
            'Doe, 
            Objet: Loyer de septembre, 
            Je soussigné Mr MEBARKI Hachemi gérant du logement situé au 87 Avenue Maréchal Foch 77500 Chelles, 
            réserve la chambre Room 101 à John Doe, pour la période du %s au %s pour la somme de 500 €. 
            A Chelles le %s.',
            now()->startOfMonth()->format('d/m/Y'),
            now()->endOfMonth()->format('d/m/Y'),
            now()->format('d/m/Y')
        );

        $this->assertEquals($expectedDescription, $description);
    }
}
