<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($reservation) {
            // Supprimer les fichiers PDF des factures associées
            foreach ($reservation->invoices as $invoice) {
                if ($invoice->pdf_path && Storage::exists($invoice->pdf_path)) {
                    Storage::delete($invoice->pdf_path);
                }
            }
        });
    }

    // Define relationships
    public function renter()
    {
        return $this->belongsTo(Renter::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relation avec les factures (une réservation peut avoir plusieurs factures)
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
