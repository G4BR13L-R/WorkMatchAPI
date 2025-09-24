<?php

namespace App\Http\Controllers\Contratante;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfertaRequest;
use App\Services\OfertaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OfertaController extends Controller
{
    public function __construct(private OfertaService $ofertaService) {}

    public function index(Request $request)
    {
        try {
            $ofertas = $this->ofertaService->listOfertasByUser($request->user());
            return response()->json($ofertas, 200);
        } catch (Exception $e) {
            Log::error('Erro ao listar as ofertas: ' . $e->getMessage());
            return response()->json(['message' => 'Não foi possível listar as ofertas, tente novamente mais tarde'], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $oferta = $this->ofertaService->getOfertaById($id);
            return response()->json($oferta, 200);
        } catch (Exception $e) {
            Log::error('Erro ao buscar a oferta: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Não foi possível buscar a oferta, tente novamente mais tarde'], 500);
        }
    }

    public function store(OfertaRequest $request)
    {
        $data = $request->validated();

        try {
            $oferta = $this->ofertaService->registerOferta($request->user(), $data);
            return response()->json($oferta, 201);
        } catch (Exception $e) {
            Log::error('Erro ao cadastrar a oferta: ' . $e->getMessage(), ['data' => $data]);
            return response()->json(['message' => 'Não foi possível criar a oferta, tente novamente mais tarde'], 500);
        }
    }

    public function update(OfertaRequest $request, int $id)
    {
        $data = $request->validated();

        try {
            $oferta = $this->ofertaService->updateOferta($request->user(), $id, $data);
            return response()->json($oferta, 200);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar a oferta: ' . $e->getMessage(), ['data' => $data, 'id' => $id]);
            return response()->json(['message' => 'Não foi possível atualizar a oferta, tente novamente mais tarde'], 500);
        }
    }

    public function destroy(Request $request, int $id)
    {
        try {
            $this->ofertaService->deleteOferta($request->user(), $id);
            return response()->json(['message' => 'Oferta deletada com sucesso!'], 200);
        } catch (Exception $e) {
            Log::error('Erro ao deletar a oferta: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Não foi possível deletar a oferta, tente novamente mais tarde'], 500);
        }
    }
}
