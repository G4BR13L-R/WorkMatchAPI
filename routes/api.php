<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Contratado\ContratadoController;
use App\Http\Controllers\Contratante\ContratanteController;
use App\Http\Controllers\Contratante\OfertaController as ContratanteOfertaController;
use App\Http\Controllers\Contratado\OfertaController as ContratadoOfertaController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

// Rotas publicas
Route::post('sessions', [AuthController::class, 'login']);
Route::post('contratante/perfil', [ContratanteController::class, 'store']);
Route::post('contratado/perfil', [ContratadoController::class, 'store']);

Route::get('estados', [LocationController::class, 'estados']);
Route::get('cidades/search/{nome}', [LocationController::class, 'cidadesByName']);
Route::get('cidades/{estadoId}', [LocationController::class, 'cidades']);

// Rotas protegidas
Route::middleware(['auth:sanctum'])->group(function () {

    Route::delete('sessions', [AuthController::class, 'logout']);

    Route::prefix('contratante')->middleware('check.type:contratante')->group(function () {

        Route::get('perfil', [ContratanteController::class, 'show']);
        Route::put('perfil', [ContratanteController::class, 'update']);
        Route::put('perfil/senha', [ContratanteController::class, 'updatePassword']);
        Route::delete('perfil', [ContratanteController::class, 'delete']);

        Route::get('ofertas', [ContratanteOfertaController::class, 'index']);
        Route::get('ofertas/{id}', [ContratanteOfertaController::class, 'show']);
        Route::post('ofertas', [ContratanteOfertaController::class, 'store']);
        Route::put('ofertas/{id}', [ContratanteOfertaController::class, 'update']);
        Route::put('ofertas/{id}/finalizar', [ContratanteOfertaController::class, 'finalizarOferta']);
        Route::delete('ofertas/{id}', [ContratanteOfertaController::class, 'destroy']);
    });

    Route::prefix('contratado')->middleware('check.type:contratado')->group(function () {

        Route::get('perfil', [ContratadoController::class, 'show']);
        Route::put('perfil', [ContratadoController::class, 'update']);
        Route::put('perfil/senha', [ContratadoController::class, 'updatePassword']);
        Route::delete('perfil', [ContratadoController::class, 'delete']);

        Route::get('ofertas', [ContratadoOfertaController::class, 'index']);
        Route::get('ofertas/search/{cidadeId}', [ContratadoOfertaController::class, 'index']);
        Route::get('ofertas/{id}', [ContratadoOfertaController::class, 'show']);
    });
});
