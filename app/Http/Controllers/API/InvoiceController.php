<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use App\Models\Invoice;

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
    public function index(): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getAllInvoices();
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
}
