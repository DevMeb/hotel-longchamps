<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Renter;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class InvoiceResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_resource_structure()
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
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        $invoice = Invoice::factory()->create([
            'reservation_id' => $reservation->id,
            'subject' => 'Test Invoice',
            'billing_start_date' => now()->startOfMonth(),
            'billing_end_date' => now()->endOfMonth(),
            'description' => 'Test description',
            'issued_at' => now(),
            'paid_at' => now(),
            'status' => Invoice::STATUS_PENDING,
        ]);

        $resource = (new InvoiceResource($invoice))->toArray(request());

        $expectedDescription = sprintf(
            "%s,
            Objet: %s,
            Je soussigné Mr MEBARKI Hachemi gérant du logement situé au 87 Avenue Maréchal Foch 77500 Chelles,
            réserve la chambre %s à %s %s, pour la période du %s au %s pour la somme de %s €.
            A Chelles le %s.",
            $renter->last_name,
            $invoice->subject,
            $room->name,
            $renter->first_name,
            $renter->last_name,
            $invoice->billing_start_date->format('d/m/Y'),
            $invoice->billing_end_date->format('d/m/Y'),
            $room->rent,
            $invoice->created_at->format('d/m/Y')
        );

        $this->assertEquals($invoice->id, $resource['id']);
        $this->assertEquals($invoice->subject, $resource['subject']);
        $this->assertEquals($expectedDescription, $resource['description']);
        $this->assertEquals(Carbon::parse($invoice->billing_start_date)->format('d/m/Y'), $resource['billing_start_date']);
        $this->assertEquals(Carbon::parse($invoice->billing_end_date)->format('d/m/Y'), $resource['billing_end_date']);
        $this->assertEquals(Carbon::parse($invoice->issued_at)->format('d/m/Y H:i:s'), $resource['issued_at']);
        $this->assertEquals(Carbon::parse($invoice->paid_at)->format('d/m/Y H:i:s'), $resource['paid_at']);

        // Teste la relation avec la réservation
        $this->assertArrayHasKey('reservation', $resource);
        $this->assertEquals($reservation->id, $resource['reservation']['id']);
    }

    public function test_invoice_resource_handles_null_values()
    {
        $renter = Renter::factory()->create();
        $room = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'renter_id' => $renter->id,
            'room_id' => $room->id,
        ]);

        $invoice = Invoice::factory()->create([
            'reservation_id' => $reservation->id,
            'issued_at' => null,
            'paid_at' => null,
        ]);

        $resource = (new InvoiceResource($invoice))->toArray(request());

        $this->assertNull($resource['issued_at']);
        $this->assertNull($resource['paid_at']);
    }
}
