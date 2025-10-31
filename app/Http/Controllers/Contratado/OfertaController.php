<?php

namespace App\Http\Controllers\Contratado;

use App\Http\Controllers\Controller;
use App\Services\Contratado\OfertaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OfertaController extends Controller
{
    public function __construct(private OfertaService $ofertaService) {}

    public function index(Request $request)
    {
        $finalizada = filter_var($request->query('finalizada'), FILTER_VALIDATE_BOOLEAN);
        $cidadeId = $request->query('cidade_id');

        try {
            $ofertas = $this->ofertaService->listOfertas($request->user(), $finalizada, $cidadeId);
            return response()->json($ofertas, 200);
        } catch (Exception $e) {
            Log::error('Erro ao listar as ofertas: ' . $e->getMessage());
            return response()->json(['message' => 'Não foi possível listar as ofertas, tente novamente mais tarde'], 500);
        }
    }

    public function show(Request $request, int $id)
    {
        try {
            $oferta = $this->ofertaService->getOfertaById($request->user(), $id);
            return response()->json($oferta, 200);
        } catch (Exception $e) {
            Log::error('Erro ao buscar a oferta: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Não foi possível buscar a oferta, tente novamente mais tarde'], 500);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'oferta_id' => 'required|integer',
            'salario' => 'nullable|numeric',
        ];

        $messages = [
            'oferta_id.required' => 'O ID da oferta é obrigatório.',
            'oferta_id.integer' => 'O ID da oferta deve ser um número inteiro.',
            'salario.numeric' => 'O salário deve ser um número válido.',
        ];

        try {
            $candidatura = $this->ofertaService->registerCandidatura($request->user(), $request->validate($rules, $messages));
            return response()->json($candidatura, 201);
        } catch (Exception $e) {
            Log::error('Erro ao criar a candidatura: ' . $e->getMessage());
            return response()->json(['message' => 'Não foi possível criar a candidatura, tente novamente mais tarde'], 500);
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->ofertaService->deleteCandidatura($request->user(), $id);
            return response()->json(['message' => 'Candidatura excluído com sucesso!']);
        } catch (Exception $e) {
            Log::error('Erro ao deletar a candidatura: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Não foi possível deletar a candidatura, tente novamente mais tarde'], 500);
        }
    }
}
