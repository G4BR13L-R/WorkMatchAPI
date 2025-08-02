<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContratanteLoginRequest;
use App\Http\Requests\ContratanteRegisterRequest;
use App\Services\Auth\ContratanteAuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContratanteAuthController extends Controller
{
    public function __construct(private ContratanteAuthService $contratanteAuthService) {}

    public function register(ContratanteRegisterRequest $request)
    {
        $data = $request->validated();

        try {
            $contratante = $this->contratanteAuthService->register($data);
            return response()->json($contratante, 201);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Erro ao registrar usuário: ' . $e->getMessage()], 500);
        }
    }

    public function login(ContratanteLoginRequest $request)
    {
        $data = $request->validated();

        $contratante = $this->contratanteAuthService->login($data);

        return response()->json($contratante, 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
}
