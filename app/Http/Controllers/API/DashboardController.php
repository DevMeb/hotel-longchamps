<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Invoice;

class DashboardController extends Controller
{

    public function getDashboardData(Request $request)
    {
        try {
            $currentYear = $request->input('year', Carbon::now()->year);
            $currentMonth = $request->input('month', Carbon::now()->month);

            // Étape 1 : Récupérer les réservations pour la période sélectionnée (sans invoices)
            $reservations = $this->getReservations($currentYear, $currentMonth);

            foreach ($reservations as $reservation) {
                // Étape 2 : Récupérer les factures groupées par statut pour chaque réservation
                $reservation->invoices = $this->getInvoiceAmountByStatus($reservation->id, $currentYear, $currentMonth);

                // Étape 3 : Calculs des montants
                $reservation->expected_amount = $this->calculateExpectedAmount($reservation->invoices);
                $reservation->actual_amount = $this->calculateActualAmount($reservation->invoices);
                $reservation->difference = $reservation->expected_amount - $reservation->actual_amount;
            }

            // Calcul des totaux globaux
            $totalExpectedAmount = $reservations->sum('expected_amount');
            $totalActualAmount = $reservations->sum('actual_amount');
            $totalDifference = $totalExpectedAmount - $totalActualAmount;

            return response()->json([
                'reservations' => $reservations,
                'total_reservations' => $reservations->count(),
                'total_expected_amount' => $totalExpectedAmount,
                'total_actual_amount' => $totalActualAmount,
                'total_difference' => $totalDifference,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des données du tableau de bord.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getReservations($year, $month)
    {
        return Reservation::where(function ($query) use ($year, $month) {
                    $query->whereYear('start_date', '<=', $year) // La réservation a commencé avant ou pendant la période sélectionnée
                        ->where(function ($subQuery) use ($year, $month) {
                            $subQuery->whereYear('end_date', '>=', $year) // Elle finit après la période sélectionnée
                                    ->orWhereNull('end_date'); // Ou bien elle est toujours en cours
                        });
                })
                ->with(['room', 'renter']) // Récupère les infos mais pas les factures ici
                ->get();
    }


    private function getInvoiceAmountByStatus($reservationId, $year, $month)
    {
        return Invoice::where('reservation_id', $reservationId)
                    ->whereYear('billing_start_date', $year)
                    ->whereMonth('billing_start_date', $month)
                    ->select(['id', 'status', 'reservation_id', 'billing_start_date', 'billing_end_date', 'subject'])
                    ->with(['reservation.room' => function ($query) {
                        $query->select('id', 'rent'); // Récupérer uniquement le montant du loyer
                    }])
                    ->get()
                    ->map(function ($invoice) {
                        return [
                            'id' => $invoice->id,
                            'status' => $invoice->status,
                            'amount' => $invoice->reservation->room->rent,
                            'subject' => $invoice->subject,
                            'billing_start_date' => $invoice->billing_start_date,
                            'billing_end_date' => $invoice->billing_end_date,
                            'reservation_id' => $invoice->reservation_id,
                        ];
                    })
                    ->groupBy('status'); // Grouper par statut
    }

    private function calculateExpectedAmount($invoicesByStatus)
    {
        return collect($invoicesByStatus)
            ->map(fn($invoices) => collect($invoices)->sum('amount'))
            ->sum();
    }

    private function calculateActualAmount($invoicesByStatus)
    {
        return collect($invoicesByStatus)->get('paid', collect())->sum('amount');
    }
}
