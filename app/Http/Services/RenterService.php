<?php

namespace App\Http\Services;

use App\Models\Renter;

class RenterService
{
    public function getAllRenters()
    {
        return Renter::all();
    }

    public function createRenter(array $data): Renter
    {
        return Renter::create($data);
    }

    public function updateRenter(Renter $renter, array $data): Renter
    {
        $renter->update($data);
        return $renter;
    }

    public function deleteRenter(Renter $renter): void
    {
        $renter->delete();
    }

    public function findRenterById($id): ?Renter
    {
        return Renter::find($id);
    }
}
