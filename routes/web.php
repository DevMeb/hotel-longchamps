<?php

use Illuminate\Support\Facades\Route;

// Route unique pour servir l'application Vue.js
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
