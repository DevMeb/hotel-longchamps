<?php

use Illuminate\Support\Facades\Route;

// 🔥 Exclure les routes API de la redirection vers Vue.js
Route::get('/{any}', function () {
    return view('app'); // Charge Vue.js
})->where('any', '^(?!api).*$'); // Exclure /api/*
