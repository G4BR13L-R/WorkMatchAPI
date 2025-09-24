<?php

namespace App\Http\Controllers\Contratado;

use App\Http\Controllers\Controller;
use App\Services\OfertaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OfertaController extends Controller
{
    public function __construct(private OfertaService $ofertaService) {}

    public function index(Request $request, ?int $cidadeId = null)
    {
        $contratado = $request->user()?->contratado;
        $endereco = $contratado?->endereco;

        if (empty($cidadeId) && !empty($endereco?->cidade_id)) {
            $cidadeId = $endereco->cidade_id;
        }

        try {
            $ofertas = $this->ofertaService->listOfertas($cidadeId);
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
}
