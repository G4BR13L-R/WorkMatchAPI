<?php

use App\Http\Controllers\Auth\ContratadoAuthController;
use App\Http\Controllers\Auth\ContratanteAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('contratante')->controller(ContratanteAuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('sessions', 'login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::delete('sessions', 'logout');
    });
});

Route::prefix('contratado')->controller(ContratadoAuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('sessions', 'login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::delete('sessions', 'logout');
    });
});
