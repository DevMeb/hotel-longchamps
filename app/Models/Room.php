<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rent',
    ];

    // Relation avec le modèle Reservation
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
