<?php

use App\Http\Controllers\Auth\ContratadoAuthController;
use App\Http\Controllers\Auth\ContratanteAuthController;
use Illuminate\Support\Facades\Route;

// Rotas publicas
Route::prefix('contratante')->controller(ContratanteAuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('sessions', 'login');
});

Route::prefix('contratado')->controller(ContratadoAuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('sessions', 'login');
});

// Rotas protegidas
Route::middleware(['auth:sanctum'])->group(function () {

    Route::delete('contratante/sessions', [ContratanteAuthController::class, 'logout']);
    Route::delete('contratado/sessions', [ContratadoAuthController::class, 'logout']);

    Route::prefix('contratante')->middleware('check.type:contratante')->group(function () {
        // Rotas protegidas para contratantes
    });

    Route::prefix('contratado')->middleware('check.type:contratado')->group(function () {
        // Rotas protegidas para contratados
    });
});
