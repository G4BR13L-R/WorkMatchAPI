<?php

namespace App\Http\Controllers\Contratante;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContratanteRegisterRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ContratanteUpdateRequest;
use App\Http\Requests\DeleteAccountRequest;
use App\Services\Contratante\ProfileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ContratanteController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    public function show(Request $request)
    {
        $contratante = $this->profileService->getProfile($request->user());

        return response()->json($contratante);
    }

    public function store(ContratanteRegisterRequest $request)
    {
        $data = $request->validated();

        try {
            $contratante = $this->profileService->registerProfile($data);
            return response()->json($contratante, 201);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao cadastrar o perfil do contratante: ' . $e->getMessage(), ['data' => $data]);
            return response()->json(['message' => 'Não foi possível criar o perfil, tente novamente mais tarde'], 500);
        }
    }

    public function update(ContratanteUpdateRequest $request)
    {
        try {
            $this->profileService->updateProfile($request->user(), $request->validated());
            return response()->json(['message' => 'Perfil atualizado com sucesso!']);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao atualizar o perfil do contratante: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'data' => $request->all()
            ]);

            return response()->json(['message' => 'Não foi possível atualizar o perfil, tente novamente mais tarde'], 500);
        }
    }

    public function updatePassword(PasswordUpdateRequest $request)
    {
        try {
            $this->profileService->updatePassword($request->user(), $request->validated());
            return response()->json(['message' => 'Senha atualizada com sucesso!']);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao atualizar senha do contratante: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'data' => $request->all()
            ]);

            return response()->json(['message' => 'Não foi possível atualizar a senha, tente novamente mais tarde'], 500);
        }
    }

    public function destroy(DeleteAccountRequest $request)
    {
        try {
            $this->profileService->deleteProfile($request->user(), $request->validated());
            return response()->json(['message' => 'Perfil deletado com sucesso!']);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao deletar o perfil do contratante: ' . $e->getMessage(), ['user_id' => $request->user()->id]);
            return response()->json(['message' => 'Não foi possível deletar o perfil, tente novamente mais tarde'], 500);
        }
    }
}
