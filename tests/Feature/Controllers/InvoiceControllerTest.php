<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\Renter;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer et authentifier un utilisateur
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
    }

    public function test_index_returns_invoices()
    {
        Invoice::factory()->count(3)->create();

        $response = $this->getJson('/api/invoices');

        //dd($response->json()); // Débogage pour voir la réponse complète

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'subject',
                    'billing_start_date',
                    'billing_end_date',
                    'status',
                    'created_at',
                    'updated_at',
                ]
            ],
            'message'
        ]);
    }

    public function test_store_creates_invoice()
    {
        $reservation = Reservation::factory()->create();

        $data = [
            'reservation_id' => $reservation->id,
            'subject' => 'Test Invoice',
            'description' => 'description',
            'billing_start_date' => now()->startOfMonth()->toDateString(),
            'billing_end_date' => now()->endOfMonth()->toDateString(),
            'status' => Invoice::STATUS_PENDING,
        ];

        $response = $this->postJson('/api/invoices', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'subject',
                'billing_start_date',
                'billing_end_date',
                'status',
                'created_at',
                'updated_at',
            ],
            'message'
        ]);

        $this->assertDatabaseHas('invoices', [
            'reservation_id' => $reservation->id,
            'subject' => 'Test Invoice',
        ]);
    }

    public function test_show_returns_invoice()
    {
        $invoice = Invoice::factory()->create();

        $response = $this->getJson('/api/invoices/' . $invoice->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'subject',
                'billing_start_date',
                'billing_end_date',
                'status',
                'created_at',
                'updated_at',
            ],
            'message'
        ]);
    }

    public function test_show_returns_404_if_not_found()
    {
        $response = $this->getJson('/api/invoices/999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Facture non trouvée.'
        ]);
    }

    public function test_update_modifies_invoice()
    {
        $invoice = Invoice::factory()->create([
            'subject' => 'Original Subject'
        ]);

        $data = [
            'reservation_id' => $invoice->reservation_id,
            'subject' => 'Updated Subject',
            'billing_start_date' => now()->startOfMonth()->toDateString(),
            'billing_end_date' => now()->endOfMonth()->toDateString(),
            'status' => Invoice::STATUS_ISSUED,
        ];

        $response = $this->putJson('/api/invoices/' . $invoice->id, $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'subject' => 'Updated Subject',
            ],
            'message' => 'Facture mise à jour avec succès.'
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'subject' => 'Updated Subject',
        ]);
    }

    public function test_update_returns_404_if_not_found()
    {
        $reservation = Reservation::factory()->create();

        $data = [
            'reservation_id' => $reservation->id, // ID de réservation valide
            'subject' => 'Updated Subject',
            'billing_start_date' => now()->startOfMonth()->toDateString(),
            'billing_end_date' => now()->endOfMonth()->toDateString(),
            'status' => Invoice::STATUS_ISSUED,
        ];

        $response = $this->putJson('/api/invoices/999', $data); // ID de facture inexistant

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Facture non trouvée.'
        ]);
    }

    public function test_destroy_deletes_invoice()
    {
        $invoice = Invoice::factory()->create();

        $response = $this->deleteJson('/api/invoices/' . $invoice->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Facture supprimée avec succès.'
        ]);

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_destroy_returns_404_if_not_found()
    {
        $response = $this->deleteJson('/api/invoices/999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Facture non trouvée.'
        ]);
    }
}
