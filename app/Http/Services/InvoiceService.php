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
