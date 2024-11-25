<?php

namespace App\Http\Services;

use App\Models\Invoice;

class InvoiceService
{
    /**
     * Récupérer toutes les factures.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllInvoices()
    {
        return Invoice::all();
    }

    /**
     * Créer une nouvelle facture.
     *
     * @param array $data
     * @return Invoice
     */
    public function createInvoice(array $data): Invoice
    {
        // Créer une instance temporaire d'Invoice pour calculer la description
        $tempInvoice = new Invoice([
            'reservation_id' => $data['reservation_id'],
            'billing_start_date' => $data['billing_start_date'],
            'billing_end_date' => $data['billing_end_date'],
            'subject' => $data['subject'], // Si nécessaire pour la description,
        ]);

        $tempInvoice->created_at = now();

        // Ajouter la description calculée dans les données
        $data['description'] = $tempInvoice->description;

        return Invoice::create($data);
    }

    /**
     * Mettre à jour une facture existante.
     *
     * @param Invoice $invoice
     * @param array $data
     * @return Invoice
     */
    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice;
    }

    /**
     * Supprimer une facture.
     *
     * @param Invoice $invoice
     * @return void
     */
    public function deleteInvoice(Invoice $invoice): void
    {
        $invoice->delete();
    }

    /**
     * Trouver une facture par ID.
     *
     * @param int $id
     * @return Invoice|null
     */
    public function findInvoiceById(int $id): ?Invoice
    {
        return Invoice::find($id);
    }
}
