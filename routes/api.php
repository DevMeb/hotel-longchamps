<?php

use App\Http\Controllers\API\InvoiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\TutorController;
use App\Http\Controllers\API\RenterController;
use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\DashboardController;

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData']);

    Route::apiResource('tutors', TutorController::class);
    Route::apiResource('renters', RenterController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('reservations', ReservationController::class);

    Route::apiResource('invoices', InvoiceController::class);
    Route::get('invoices/{id}/pdf', [InvoiceController::class, 'displayPdf'])->name('invoices.displayPdf');
    Route::post('/invoices/{id}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.sendEmail');
    Route::patch('invoices/{id}/paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.markAsPaid');

    Route::get('/validate-token', function () {
        return response()->json(['valid' => true]);
    });
});