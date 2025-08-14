<?php

namespace App\Http\Controllers\Contratado;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContratadoRegisterRequest;
use App\Services\Contratado\ContratadoProfileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContratadoController extends Controller
{
    public function __construct(private ContratadoProfileService $contratadoProfileService) {}

    public function show(Request $request)
    {
        $contratante = $this->contratadoProfileService->getProfile($request->user());

        return response()->json($contratante);
    }

    public function store(ContratadoRegisterRequest $request)
    {
        $data = $request->validated();

        try {
            $contratado = $this->contratadoProfileService->registerProfile($data);
            return response()->json($contratado, 201);
        } catch (Exception $e) {
            Log::error('Erro ao cadastrar o perfil do contratado: ' . $e->getMessage(), ['data' => $data]);
            return response()->json(['error' => 'Não foi possível criar o perfil, tente novamente mais tarde'], 500);
        }
    }
}
