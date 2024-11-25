<?php

namespace Tests\Feature\Services;

use App\Http\Services\InvoiceService;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $invoiceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService = new InvoiceService();
    }

    public function test_it_can_get_all_invoices()
    {
        Invoice::factory()->count(3)->create();

        $invoices = $this->invoiceService->getAllInvoices();

        $this->assertCount(3, $invoices);
    }

    public function test_it_can_create_an_invoice()
    {

        $reservation = \App\Models\Reservation::factory()->create();  // Créer une réservation

        $data = [
            'reservation_id' => $reservation->id,
            'subject' => 'Test Invoice',
            'description' => 'Test description',
            'billing_start_date' => now()->startOfMonth(),
            'billing_end_date' => now()->endOfMonth(),
            'issued_at' => now(),
            'status' => Invoice::STATUS_PENDING,
        ];

        $invoice = $this->invoiceService->createInvoice($data);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('Test Invoice', $invoice->subject);
    }

    public function test_it_can_update_an_invoice()
    {
        $invoice = Invoice::factory()->create([
            'subject' => 'Old Subject',
        ]);

        $data = [
            'subject' => 'Updated Subject',
        ];

        $updatedInvoice = $this->invoiceService->updateInvoice($invoice, $data);

        $this->assertEquals('Updated Subject', $updatedInvoice->subject);
    }

    public function test_it_can_delete_an_invoice()
    {
        $invoice = Invoice::factory()->create();

        $this->invoiceService->deleteInvoice($invoice);

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_it_can_find_invoice_by_id()
    {
        $invoice = Invoice::factory()->create();

        $foundInvoice = $this->invoiceService->findInvoiceById($invoice->id);

        $this->assertInstanceOf(Invoice::class, $foundInvoice);
        $this->assertEquals($invoice->id, $foundInvoice->id);
    }

    public function test_it_returns_null_when_invoice_not_found()
    {
        $foundInvoice = $this->invoiceService->findInvoiceById(999);

        $this->assertNull($foundInvoice);
    }
}
