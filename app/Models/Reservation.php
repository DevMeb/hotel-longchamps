<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Specify the fields that are mass assignable
    protected $fillable = [
        'renter_id',
        'room_id',
        'start_date',
        'end_date',
    ];

    // Define relationships
    public function renter()
    {
        return $this->belongsTo(Renter::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
