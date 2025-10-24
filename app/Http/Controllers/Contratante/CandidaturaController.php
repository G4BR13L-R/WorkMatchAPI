<?php

namespace App\Http\Controllers\Contratante;

use App\Http\Controllers\Controller;
use App\Services\CandidaturaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CandidaturaController extends Controller
{
    public function __construct(private CandidaturaService $candidaturaService) {}

    public function show(int $id)
    {
        try {
            $candidatura = $this->candidaturaService->getCandidaturaById($id);
            return response()->json($candidatura, 200);
        } catch (Exception $e) {
            Log::error('Erro ao buscar a candidatura: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Não foi possível buscar a candidatura, tente novamente mais tarde'], 500);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        $rules = ['status_id' => 'required|exists:status,id'];
        $feedback = ['status_id.required' => 'O campo status é obrigatório', 'status_id.exists' => 'O status informado é inválido'];

        $data = $request->validate($rules, $feedback);

        try {
            $candidatura = $this->candidaturaService->changeStatus($id, $data['status_id']);
            return response()->json($candidatura, 200);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar o status da candidatura: ' . $e->getMessage(), ['id' => $id, 'status' => $data['status_id']]);
            return response()->json(['message' => 'Não foi possível atualizar o status da candidatura, tente novamente mais tarde'], 500);
        }
    }
}
