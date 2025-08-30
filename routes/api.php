<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Contratado\ContratadoController;
use App\Http\Controllers\Contratante\ContratanteController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

// Rotas publicas
Route::post('sessions', [AuthController::class, 'login']);
Route::post('contratante/perfil', [ContratanteController::class, 'store']);
Route::post('contratado/perfil', [ContratadoController::class, 'store']);

Route::get('estados', [LocationController::class, 'estados']);
Route::get('cidades/{estadoId}', [LocationController::class, 'cidades']);

// Rotas protegidas
Route::middleware(['auth:sanctum'])->group(function () {

    Route::delete('sessions', [AuthController::class, 'logout']);

    Route::prefix('contratante')->middleware('check.type:contratante')->group(function () {

        Route::get('perfil', [ContratanteController::class, 'show']);
        Route::put('perfil', [ContratanteController::class, 'update']);
        Route::put('perfil/senha', [ContratanteController::class, 'updatePassword']);
        Route::delete('perfil', [ContratanteController::class, 'delete']);
    });

    Route::prefix('contratado')->middleware('check.type:contratado')->group(function () {

        Route::get('perfil', [ContratadoController::class, 'show']);
        Route::put('perfil', [ContratadoController::class, 'update']);
        Route::put('perfil/senha', [ContratadoController::class, 'updatePassword']);
        Route::delete('perfil', [ContratadoController::class, 'delete']);
    });
});
