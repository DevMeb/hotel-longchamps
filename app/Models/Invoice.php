<?php

namespace App\Models;

use App\Http\Resources\RoomResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    //AJOUTER UNE DATE DE DEBUT DE FACTURATION ET UNE DATE DE FIN
    protected $fillable = [
        'reservation_id',
        'subject',
        'billing_start_date',
        'billing_end_date',
        'description',
        'issued_at',
        'paid_at',
        'status',
    ];

    // Relation avec la réservation
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Constantes pour les statuts
    const STATUS_PENDING = 'pending';
    const STATUS_ISSUED = 'issued';
    const STATUS_PAID = 'paid';

    // Méthodes pour mettre à jour le statut
    public function markAsPending()
    {
        $this->update(['status' => self::STATUS_PENDING]);
    }

    public function markAsIssued()
    {
        $this->update(['status' => self::STATUS_ISSUED, 'issued_at' => now()]);
    }

    public function markAsPaid()
    {
        $this->update(['status' => self::STATUS_PAID, 'paid_at' => now()]);
    }

    // Méthode pour générer dynamiquement la désignation
    public function getDescriptionAttribute()
    {
        $roomResource = new RoomResource($this->reservation->room);

        return sprintf(
            '%s, 
            Objet: %s, 
            Je soussigné Mr MEBARKI Hachemi gérant du logement situé au 87 Avenue Maréchal Foch 77500 Chelles, 
            réserve la chambre %s à %s %s, pour la période du %s au %s pour la somme de %s €. 
            A Chelles le %s.',
            $this->reservation->renter->last_name, // Nom du locataire
            $this->subject, // Sujet de la facture
            $this->reservation->room->name, // Nom de la chambre
            $this->reservation->renter->first_name, // Prénom du locataire
            $this->reservation->renter->last_name, // Nom du locataire
            $this->billing_start_date->format('d/m/Y'), // Date de début de la période de facturation
            $this->billing_end_date->format('d/m/Y'), // Date de fin de la période de facturation
            $roomResource->rent, // Montant de la chambre en euros
            $this->created_at->format('d/m/Y') // Date de création de la facture
        );
    }
}
