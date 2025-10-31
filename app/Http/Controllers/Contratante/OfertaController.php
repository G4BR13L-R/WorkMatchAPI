<?php

namespace App\Http\Controllers\Contratante;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfertaRequest;
use App\Services\Contratante\OfertaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OfertaController extends Controller
{
    public function __construct(private OfertaService $ofertaService) {}

    public function index(Request $request)
    {
        $finalizada = filter_var($request->query('finalizada'), FILTER_VALIDATE_BOOLEAN);

        try {
            $ofertas = $this->ofertaService->getOfertas($request->user(), $finalizada);
            return response()->json($ofertas, 200);
        } catch (HttpException $e) {
            throw $e;
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
        } catch (HttpException $e) {
            throw $e;
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
        } catch (HttpException $e) {
            throw $e;
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
        } catch (HttpException $e) {
            throw $e;
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
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao deletar a oferta: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Não foi possível deletar a oferta, tente novamente mais tarde'], 500);
        }
    }

    public function finalizarOferta(Request $request, int $id)
    {
        try {
            $oferta = $this->ofertaService->finalizarOferta($request->user(), $id);
            return response()->json($oferta, 200);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao alterar o status da oferta: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Não foi possível alterar o status da oferta, tente novamente mais tarde'], 500);
        }
    }

    public function candidatosOferta(int $id)
    {
        try {
            $candidatos = $this->ofertaService->getCandidatosByOfertaId($id);
            return response()->json($candidatos, 200);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao buscar os candidatos da oferta: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Não foi possível buscar os candidatos da oferta, tente novamente mais tarde'], 500);
        }
    }
}
