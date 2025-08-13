<?php

namespace App\Http\Controllers\Contratante;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContratanteUpdatePasswordRequest;
use App\Http\Requests\ContratanteUpdateRequest;
use App\Services\Contratante\ContratanteProfileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContratanteController extends Controller
{
    public function __construct(private ContratanteProfileService $contratanteProfileService) {}

    public function show(Request $request)
    {
        $contratante = $this->contratanteProfileService->getProfile($request->user());

        return response()->json($contratante);
    }

    public function update(ContratanteUpdateRequest $request)
    {
        try {
            $this->contratanteProfileService->updateProfile($request->user(), $request->validated());
            return response()->json(['message' => 'Perfil atualizado com sucesso!']);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar perfil do contratante: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'data' => $request->all()
            ]);

            return response()->json(['error' => 'Não foi possível atualizar o perfil, tente novamente mais tarde'], 500);
        }
    }

    public function updatePassword(ContratanteUpdatePasswordRequest $request)
    {
        try {
            $this->contratanteProfileService->updatePassword($request->user(), $request->validated());
            return response()->json(['message' => 'Senha atualizada com sucesso!']);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar senha do contratante: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'data' => $request->all()
            ]);

            return response()->json(['error' => 'Não foi possível atualizar a senha, tente novamente mais tarde'], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $this->contratanteProfileService->deleteProfile($request->user());
            return response()->json(['message' => 'Perfil deletado com sucesso!']);
        } catch (Exception $e) {
            Log::error('Erro ao deletar perfil do contratante: ' . $e->getMessage(), ['user_id' => $request->user()->id]);
            return response()->json(['error' => 'Não foi possível deletar o perfil, tente novamente mais tarde'], 500);
        }
    }
}
