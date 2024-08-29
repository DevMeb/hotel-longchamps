<?php

namespace Tests\Unit;

use App\Http\Requests\InvoiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class InvoiceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function getValidatorInstance(array $data)
    {
        $request = new InvoiceRequest();
        return Validator::make($data, $request->rules(), $request->messages());
    }

    public function test_it_validates_reservation_id_is_required()
    {
        $data = [
            'reservation_id' => null,
            'subject' => 'Loyer de septembre',
            'billing_start_date' => now()->startOfMonth(),
            'billing_end_date' => now()->endOfMonth(),
            'status' => 'pending',
        ];

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
        $this->assertEquals('L\'ID de la réservation est obligatoire.', $validator->errors()->first('reservation_id'));
    }

    public function test_it_validates_reservation_id_exists()
    {
        $data = [
            'reservation_id' => 999, // ID qui n'existe pas
            'subject' => 'Loyer de septembre',
            'billing_start_date' => now()->startOfMonth(),
            'billing_end_date' => now()->endOfMonth(),
            'status' => 'pending',
        ];

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
        $this->assertEquals('La réservation spécifiée n\'existe pas.', $validator->errors()->first('reservation_id'));
    }

    public function test_it_validates_subject_is_required()
    {
        $data = [
            'reservation_id' => 1,
            'subject' => null,
            'billing_start_date' => now()->startOfMonth(),
            'billing_end_date' => now()->endOfMonth(),
            'status' => 'pending',
        ];

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
        $this->assertEquals('Le sujet est obligatoire.', $validator->errors()->first('subject'));
    }

    public function test_it_validates_billing_dates_are_required_and_valid()
    {
        $data = [
            'reservation_id' => 1,
            'subject' => 'Loyer de septembre',
            'billing_start_date' => null,
            'billing_end_date' => now()->endOfMonth(),
            'status' => 'pending',
        ];

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
        $this->assertEquals('La date de début de facturation est obligatoire.', $validator->errors()->first('billing_start_date'));

        $data['billing_start_date'] = now()->startOfMonth();
        $data['billing_end_date'] = null;

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
        $this->assertEquals('La date de fin de facturation est obligatoire.', $validator->errors()->first('billing_end_date'));
    }

    public function test_it_validates_billing_dates_order()
    {
        $data = [
            'reservation_id' => 1,
            'subject' => 'Loyer de septembre',
            'billing_start_date' => now()->endOfMonth(),
            'billing_end_date' => now()->startOfMonth(),
            'status' => 'pending',
        ];

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
        $this->assertEquals('La date de début de facturation doit être antérieure ou égale à la date de fin de facturation.', $validator->errors()->first('billing_start_date'));

        $data['billing_end_date'] = now()->endOfMonth();
        $data['billing_start_date'] = now()->startOfMonth();

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
    }

    public function test_it_validates_status_is_required_and_valid()
    {
        $data = [
            'reservation_id' => 1,
            'subject' => 'Loyer de septembre',
            'billing_start_date' => now()->startOfMonth(),
            'billing_end_date' => now()->endOfMonth(),
            'status' => null,
        ];

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
        $this->assertEquals('Le statut est obligatoire.', $validator->errors()->first('status'));

        $data['status'] = 'invalid_status';

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
        $this->assertEquals('Le statut doit être l\'une des valeurs suivantes : pending, issued, paid.', $validator->errors()->first('status'));

        $data['status'] = 'pending';

        $validator = $this->getValidatorInstance($data);
        $this->assertTrue($validator->fails());
    }
}
