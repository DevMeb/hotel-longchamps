<?php

namespace App\Http\Services;

use App\Models\Invoice;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        // Vérifiez si le chemin du fichier PDF existe
        if ($invoice->pdf_path && Storage::exists($invoice->pdf_path)) {
            // Supprimez le fichier PDF
            Storage::delete($invoice->pdf_path);
        }

        // Supprimez l'enregistrement de la facture
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

    /**
     * Télécharge une facture au format PDF
     * @param Invoice $invoice
    */
    public function downloadPdf(Invoice $invoice) 
    {
        $idInvoice = $invoice->id;
        $roomName = $invoice->reservation->room->name;
        $roomerName = $invoice->reservation->renter->first_name;
        $subject = $invoice->subject;

        $pdfName = str_replace([' ', '/', '\\'], '_', "{$roomerName}_{$roomName}_{$subject}_{$idInvoice}.pdf");
        $path = 'invoices/' . $pdfName;

        //dd($invoice->toArray());

        $pdf = Pdf::loadView('invoices/pdf', ["invoice" => $invoice]);

        Storage::put($path, $pdf->output());

        $invoice->update(['pdf_path' => $path]);
    }

    public function searchPdfInStorage(string $pdfPath) {
        // Vérifier si le fichier existe
        if (!Storage::exists($pdfPath)) {
            throw new \Exception("Le fichier PDF n'existe pas dans le stockage.");
        }

        // Récupérer le contenu du fichier pour affichage
        return Storage::get($pdfPath);
    }

}
