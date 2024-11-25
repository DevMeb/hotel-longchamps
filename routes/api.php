<?php

use App\Http\Controllers\API\InvoiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\TutorController;
use App\Http\Controllers\API\RenterController;
use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\ReservationController;

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::apiResource('tutors', TutorController::class);
    Route::apiResource('renters', RenterController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('reservations', ReservationController::class);

    Route::apiResource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.download');

    Route::get('/validate-token', function () {
        return response()->json(['valid' => true]);
    });
});