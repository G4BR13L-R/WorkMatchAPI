<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Estado;
use Exception;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function estados()
    {
        $estados = Estado::orderBy('descricao')->get();

        if ($estados->isEmpty()) {
            Log::info('Nenhum estado encontrado');
            return response()->json(['message' => 'Nenhum estado encontrado'], 404);
        }

        return response()->json($estados);
    }

    public function cidades(int $estadoId)
    {
        try {
            $estado = Estado::findOrFail($estadoId);

            $cidades = $estado->cidades()
                ->with('estado')
                ->orderBy('descricao')
                ->get();

            if ($cidades->isEmpty()) {
                Log::info('Nenhuma cidade encontrada para o estado', ['estado_id' => $estadoId]);
                return response()->json(['message' => 'Nenhuma cidade encontrada'], 404);
            }

            return response()->json($cidades);
        } catch (Exception $e) {
            Log::error('Erro ao buscar cidades do estado: ' . $e->getMessage(), [
                'estado_id' => $estadoId,
            ]);

            return response()->json(['message' => 'Erro ao buscar cidades'], 500);
        }
    }

    public function cidadesByName(string $cidade)
    {
        try {
            $cidades = Cidade::with('estado')
                ->where('descricao', 'ILIKE', "%{$cidade}%")
                ->orderBy('descricao')
                ->get();

            if ($cidades->isEmpty()) {
                Log::info('Nenhuma cidade encontrada com o nome informado', ['cidade' => $cidade]);
                return response()->json(['message' => 'Nenhuma cidade encontrada'], 404);
            }

            return response()->json($cidades);
        } catch (Exception $e) {
            Log::error('Erro ao buscar cidades pelo nome: ' . $e->getMessage(), [
                'cidade' => $cidade,
            ]);

            return response()->json(['message' => 'Erro ao buscar cidades'], 500);
        }
    }
}
