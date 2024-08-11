<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\TutorController;

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::apiResource('tutors', TutorController::class);

    Route::get('/validate-token', function () {
        return response()->json(['valid' => true]);
    });
});