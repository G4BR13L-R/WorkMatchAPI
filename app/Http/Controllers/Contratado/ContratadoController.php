<?php

namespace App\Http\Controllers\Contratado;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContratadoRegisterRequest;
use App\Http\Requests\ContratadoUpdateRequest;
use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Services\Contratado\ProfileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ContratadoController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    public function show(Request $request)
    {
        $contratado = $this->profileService->getProfile($request->user());

        return response()->json($contratado);
    }

    public function store(ContratadoRegisterRequest $request)
    {
        $data = $request->validated();

        try {
            $contratado = $this->profileService->registerProfile($data);
            return response()->json($contratado, 201);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao cadastrar o perfil do contratado: ' . $e->getMessage(), ['data' => $data]);
            return response()->json(['message' => 'Não foi possível criar o perfil, tente novamente mais tarde'], 500);
        }
    }

    public function update(ContratadoUpdateRequest $request)
    {
        try {
            $this->profileService->updateProfile($request->user(), $request->validated());
            return response()->json(['message' => 'Perfil atualizado com sucesso!']);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao atualizar o perfil do contratado: ' . $e->getMessage(), [
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
            Log::error('Erro ao atualizar a senha do contratado: ' . $e->getMessage(), [
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
            return response()->json(['message' => 'Perfil excluído com sucesso!']);
        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao excluir o perfil do contratado: ' . $e->getMessage(), [
                'user_id' => $request->user()->id
            ]);

            return response()->json(['message' => 'Não foi possível excluir o perfil, tente novamente mais tarde'], 500);
        }
    }
}
