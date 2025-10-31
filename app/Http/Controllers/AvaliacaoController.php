<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvaliacaoRequest;
use App\Services\AvaliacaoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AvaliacaoController extends Controller
{
    public function __construct(private AvaliacaoService $avaliacaoService) {}

    public function show(Request $request)
    {
        $rules = [
            'autor_id' => 'required|integer',
            'autor_tipo' => 'required|string|in:contratante,contratado',
            'destinatario_id' => 'required|integer',
            'destinatario_tipo' => 'required|string|in:contratante,contratado',
            'oferta_id' => 'required|integer|exists:ofertas,id',
        ];

        $messages = [
            'autor_id.required' => 'O ID do autor é obrigatório.',
            'autor_id.integer' => 'O ID do autor deve ser um número inteiro.',
            'autor_tipo.required' => 'O tipo do autor é obrigatório.',
            'autor_tipo.in' => 'O tipo do autor deve ser "contratante" ou "contratado".',
            'destinatario_id.required' => 'O ID do destinatário é obrigatório.',
            'destinatario_id.integer' => 'O ID do destinatário deve ser um número inteiro.',
            'destinatario_tipo.required' => 'O tipo do destinatário é obrigatório.',
            'destinatario_tipo.in' => 'O tipo do destinatário deve ser "contratante" ou "contratado".',
            'oferta_id.required' => 'O ID da oferta é obrigatório.',
            'oferta_id.integer' => 'O ID da oferta deve ser um número inteiro.',
            'oferta_id.exists' => 'O ID da oferta deve existir na tabela de ofertas.',
        ];

        $dados = $request->validate($rules, $messages);

        try {
            $avaliacao = $this->avaliacaoService->getAvaliacao($dados);
            return response()->json($avaliacao, 200);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao buscar a avaliação: ' . $e->getMessage(), $dados);
            return response()->json(['message' => 'Não foi possível buscar a avaliação, tente novamente mais tarde'], 500);
        }
    }

    public function store(AvaliacaoRequest $request)
    {
        $dados = $request->validated();

        try {
            $avaliacao = $this->avaliacaoService->registerAvaliacao($dados);
            return response()->json($avaliacao, 201);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao registrar a avaliação: ' . $e->getMessage(), $dados);
            return response()->json(['message' => 'Não foi possível registrar a avaliação, tente novamente mais tarde'], 500);
        }
    }

    public function update(AvaliacaoRequest $request, int $id)
    {
        $dados = $request->validated();

        try {
            $avaliacao = $this->avaliacaoService->updateAvaliacao($dados, $id);
            return response()->json($avaliacao, 200);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao atualizar a avaliação: ' . $e->getMessage(), array_merge($dados, ['id' => $id]));
            return response()->json(['message' => 'Não foi possível atualizar a avaliação, tente novamente mais tarde'], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->avaliacaoService->deleteAvaliacao($id);
            return response()->json(['message' => 'Avaliação deletada com sucesso'], 200);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao deletar a avaliação: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['message' => 'Não foi possível deletar a avaliação, tente novamente mais tarde'], 500);
        }
    }
}
