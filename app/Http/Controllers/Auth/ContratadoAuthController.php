<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContratadoLoginRequest;
use App\Http\Requests\ContratadoRegisterRequest;
use App\Services\Auth\ContratadoAuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContratadoAuthController extends Controller
{
    public function __construct(private ContratadoAuthService $contratadoAuthService) {}

    public function register(ContratadoRegisterRequest $request)
    {
        $data = $request->validated();

        try {
            $contratado = $this->contratadoAuthService->register($data);
            return response()->json($contratado, 201);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Erro ao registrar usuário: ' . $e->getMessage()], 500);
        }
    }

    public function login(ContratadoLoginRequest $request)
    {
        $data = $request->validated();

        $contratado = $this->contratadoAuthService->login($data);

        return response()->json($contratado, 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
}
