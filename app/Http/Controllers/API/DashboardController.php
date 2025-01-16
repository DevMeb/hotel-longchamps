<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Invoice;

class DashboardController extends Controller
{
    // Statuts de facture
    private const STATUS_PENDING = 'pending';
    private const STATUS_ISSUED = 'issued';
    private const STATUS_PAID = 'paid';

    public function getDashboardData(Request $request)
    {
        try {
            $currentYear = $request->input('year', Carbon::now()->year);
            $currentMonth = $request->input('month', Carbon::now()->month);

            // Calculs
            $reservationCount = $this->getReservationCount($currentYear, $currentMonth);
            $invoiceCount = $this->getInvoiceCount($currentYear, $currentMonth);
            $potentialRevenue = $this->calculatePotentialRevenue($currentYear, $currentMonth);
            $actualRevenue = $this->getActualRevenue($currentYear, $currentMonth);
            $unsentInvoices = $this->getUnsentInvoices($currentYear, $currentMonth);
            $sentUnpaidInvoices = $this->getSentUnpaidInvoices($currentYear, $currentMonth);
            $reservationsWithoutInvoices = $this->getReservationsWithoutInvoices($currentYear, $currentMonth);
            $sentPaidInvoices = $this->getSentPaidInvoices($currentYear, $currentMonth);

            // Calcul de la différence de CA
            $revenueDifference = ($actualRevenue - $potentialRevenue) / 100;

            return response()->json([
                'reservationCount' => $reservationCount,
                'invoiceCount' => $invoiceCount,
                'potentialRevenue' => $potentialRevenue / 100,
                'actualRevenue' => $actualRevenue / 100,
                'unsentInvoices' => $unsentInvoices,
                'sentUnpaidInvoices' => $sentUnpaidInvoices,
                'reservationsWithoutInvoices' => $reservationsWithoutInvoices,
                'revenueDifference' => $revenueDifference,
                'sentPaidInvoices' => $sentPaidInvoices,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des données du tableau de bord.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Méthodes dédiées

    private function filterByMonthAndYear($query, $year, $month)
    {
        return $query->whereYear('start_date', $year)
                     ->whereMonth('start_date', $month)
                     ->orWhere(function ($subQuery) use ($year, $month) {
                         $subQuery->whereYear('end_date', $year)
                                  ->whereMonth('end_date', $month);
                     })
                     ->orWhere(function ($subQuery) use ($year, $month) {
                         $subQuery->whereYear('start_date', '<=', $year)
                                  ->whereYear('end_date', '>=', $year)
                                  ->whereMonth('start_date', '<=', $month)
                                  ->whereMonth('end_date', '>=', $month);
                     });
    }

    private function getReservationCount($year, $month)
    {
        return Reservation::where(function ($query) use ($year, $month) {
            $this->filterByMonthAndYear($query, $year, $month);
        })->count();
    }

    private function getInvoiceCount($year, $month)
    {
        return Invoice::whereHas('reservation', function ($query) use ($year, $month) {
            $this->filterByMonthAndYear($query, $year, $month);
        })->count();
    }

    private function calculatePotentialRevenue($year, $month)
    {
        $reservationsWithoutInvoices = $this->getReservationsWithoutInvoices($year, $month)
            ->sum(fn($reservation) => $reservation['amount'] * 100);

        $unsentInvoices = $this->getUnsentInvoices($year, $month)
            ->sum(fn($invoice) => $invoice['amount'] * 100);

        $sentUnpaidInvoices = $this->getSentUnpaidInvoices($year, $month)
            ->sum(fn($invoice) => $invoice['amount'] * 100);

        $sentPaidInvoices = $this->getSentPaidInvoices($year, $month)
            ->sum(fn($invoice) => $invoice['amount'] * 100);

        return $reservationsWithoutInvoices + $unsentInvoices + $sentUnpaidInvoices + $sentPaidInvoices;
    }

    private function getActualRevenue($year, $month)
    {
        return Invoice::where('status', self::STATUS_PAID)
                      ->whereHas('reservation', function ($query) use ($year, $month) {
                          $this->filterByMonthAndYear($query, $year, $month);
                      })
                      ->join('reservations', 'invoices.reservation_id', '=', 'reservations.id')
                      ->join('rooms', 'reservations.room_id', '=', 'rooms.id')
                      ->sum('rooms.rent');
    }

    private function getUnsentInvoices($year, $month)
    {
        return Invoice::whereNull('issued_at')
                      ->whereNull('paid_at')
                      ->where('status', self::STATUS_PENDING)
                      ->whereHas('reservation', function ($query) use ($year, $month) {
                          $this->filterByMonthAndYear($query, $year, $month);
                      })
                      ->with(['reservation.room'])
                      ->get()
                      ->map(function ($invoice) {
                          return [
                              'id' => $invoice->id,
                              'subject' => $invoice->subject,
                              'status' => $invoice->status,
                              'amount' => $invoice->reservation->room->rent / 100,
                              'reservation_id' => $invoice->reservation->id,
                          ];
                      });
    }

    private function getSentUnpaidInvoices($year, $month)
    {
        return Invoice::whereNotNull('issued_at')
                      ->whereNull('paid_at')
                      ->where('status', self::STATUS_ISSUED)
                      ->whereHas('reservation', function ($query) use ($year, $month) {
                          $this->filterByMonthAndYear($query, $year, $month);
                      })
                      ->with(['reservation.room'])
                      ->get()
                      ->map(function ($invoice) {
                          return [
                              'id' => $invoice->id,
                              'subject' => $invoice->subject,
                              'issued_at' => $invoice->issued_at,
                              'status' => $invoice->status,
                              'amount' => $invoice->reservation->room->rent / 100,
                              'reservation_id' => $invoice->reservation->id,
                          ];
                      });
    }

    private function getReservationsWithoutInvoices($year, $month)
    {
        return Reservation::where(function ($query) use ($year, $month) {
            $this->filterByMonthAndYear($query, $year, $month);
        })
        ->whereDoesntHave('invoices')
        ->with(['room', 'renter'])
        ->get()
        ->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'start_date' => $reservation->start_date,
                'end_date' => $reservation->end_date,
                'room_name' => $reservation->room->name,
                'renter_name' => $reservation->renter->first_name . ' ' . $reservation->renter->last_name,
                'amount' => $reservation->room->rent / 100,
            ];
        });
    }

    private function getSentPaidInvoices($year, $month)
    {
        return Invoice::whereNotNull('issued_at')
                      ->whereNotNull('paid_at')
                      ->where('status', self::STATUS_PAID)
                      ->whereHas('reservation', function ($query) use ($year, $month) {
                          $this->filterByMonthAndYear($query, $year, $month);
                      })
                      ->with(['reservation.room'])
                      ->get()
                      ->map(function ($invoice) {
                          return [
                              'id' => $invoice->id,
                              'subject' => $invoice->subject,
                              'issued_at' => $invoice->issued_at,
                              'paid_at' => $invoice->paid_at,
                              'status' => $invoice->status,
                              'amount' => $invoice->reservation->room->rent / 100,
                              'reservation_id' => $invoice->reservation->id,
                          ];
                      });
    }
}
