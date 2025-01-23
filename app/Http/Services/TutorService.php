<?php

namespace App\Http\Services;

use App\Models\Tutor;

class TutorService
{
    public function getAllTutors()
    {
        return Tutor::orderBy('last_name', 'asc')
                    ->orderBy('first_name', 'asc')
                    ->get();
    }

    public function createTutor(array $data): Tutor
    {
        return Tutor::create($data);
    }

    public function updateTutor(Tutor $tutor, array $data): Tutor
    {
        $tutor->update($data);
        return $tutor;
    }

    public function deleteTutor(Tutor $tutor): void
    {
        $tutor->delete();
    }

    public function findTutorById($id): ?Tutor
    {
        return Tutor::find($id);
    }
}
