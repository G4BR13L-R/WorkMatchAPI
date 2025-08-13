<?php

use App\Http\Controllers\Auth\ContratadoAuthController;
use App\Http\Controllers\Auth\ContratanteAuthController;
use App\Http\Controllers\Contratante\ContratanteController;
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

        Route::get('perfil', [ContratanteController::class, 'show']);
        Route::put('perfil', [ContratanteController::class, 'update']);
        Route::put('perfil/senha', [ContratanteController::class, 'updatePassword']);
        Route::delete('perfil', [ContratanteController::class, 'delete']);
    });

    Route::prefix('contratado')->middleware('check.type:contratado')->group(function () {
        // Rotas protegidas para contratados
    });
});
