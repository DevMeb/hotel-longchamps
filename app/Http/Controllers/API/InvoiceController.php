<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use App\Models\Invoice;
use Exception;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Récupérer toutes les factures.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $id = $request->query('id');
            $invoices = $this->invoiceService->getAllInvoices($id);
            return $this->sendResponse(InvoiceResource::collection($invoices), 'Factures récupérées avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération des factures : ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Enregistrer une nouvelle facture.
     *
     * @param RequestInvoice $request
     * @return JsonResponse
     */
    public function store(InvoiceRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated());
            
            $this->invoiceService->downloadPdf($invoice);
            
            return $this->sendResponse(new InvoiceResource($invoice), 'Facture créée avec succès.', 201);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la création de la facture : ' . $e->getMessage(), ['request' => $request->validated()], 500);
        }
    }

    /**
     * Afficher la facture spécifiée.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->findInvoiceById($id);
            if (!$invoice) {
                return $this->sendError('Facture non trouvée.', ['id_invoice' => $id], 404);
            }
            return $this->sendResponse(new InvoiceResource($invoice), 'Facture récupérée avec succès.');
        } catch (\Exception $e) {
            return $this->sendError('Échec de la récupération de la facture : ' . $e->getMessage(), ['id_invoice' => $id], 500);
        }
    }

    /**
     * Mettre à jour la facture spécifiée.
     *
     * @param RequestInvoice $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(InvoiceRequest $request, int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->findInvoiceById($id);
            
            if (!$invoice) {
                return $this->sendError('Facture non trouvée.', ['id_invoice' => $id], 404);
            }

            $updatedInvoice = $this->invoiceService->updateInvoice($invoice, $request->validated());

            if ($request->input('status') === 'paid') {
                $invoice->markAsPaid();
            }

            return $this->sendResponse(new InvoiceResource($updatedInvoice), 'Facture mise à jour avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la mise à jour de la facture : ' . $e->getMessage(), ['request' => $request->validated()], 500);
        }
    }

    /**
     * Supprimer la facture spécifiée.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->findInvoiceById($id);

            if (!$invoice) {
                return $this->sendError('Facture non trouvée.', [], 404);
            }

            $this->invoiceService->deleteInvoice($invoice);
            return $this->sendResponse(['invoice' => $invoice], 'Facture supprimée avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError('Échec de la suppression de la facture : ' . $e->getMessage(), [], 500);
        }
    }

    public function displayPdf(int $id)
    {
        try {
            $invoice = $this->invoiceService->findInvoiceById($id);

            // Rechercher le PDF dans le stockage
            $pdfContent = $this->invoiceService->searchPdfInStorage($invoice->pdf_path);

            // Retourner une réponse avec le contenu du PDF
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($invoice->pdf_path) . '"',
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 404);
        }
    }

    public function sendEmail(Request $request, int $id)
    {
        try {
            $invoice = $this->invoiceService->findInvoiceById($id);

            // Récupérer et valider les emails
            $emails = explode(',', $request->input('emails', ''));

            // Utiliser le service pour envoyer l'email
            $this->invoiceService->sendInvoiceByEmail($invoice, $emails);

            $invoice->markAsIssued();

            return $this->sendResponse(new InvoiceResource($invoice), 'Email envoyé avec succès.', 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 404);
        }
    }
}
