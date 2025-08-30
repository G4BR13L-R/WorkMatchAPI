<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Exception;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function estados()
    {
        $estados = Estado::orderBy('descricao')->get();

        if (empty($estados)) {
            Log::info('Nenhum estado encontrado');
            return response()->json(['message' => 'Nenhum estado encontrado'], 404);
        }

        return response()->json($estados, 200);
    }

    public function cidades(int $estadoId)
    {
        try {
            $cidades = Estado::findOrFail($estadoId)->cidades()->orderBy('descricao')->get();
            return response()->json($cidades, 200);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar o perfil do contratante: ' . $e->getMessage(), [
                'estado_id' => $estadoId,
            ]);

            return response()->json(['message' => 'Nenhuma cidade encontrada'], 404);
        }
    }
}
