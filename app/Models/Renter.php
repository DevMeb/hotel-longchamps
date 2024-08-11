<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renter extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'tutor_id',
    ];

    /**
     * Get the tutor associated with the renter.
     */
    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}
