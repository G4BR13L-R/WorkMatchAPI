<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContratadoLoginRequest;
use App\Http\Requests\ContratadoRegisterRequest;
use App\Models\Contratado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ContratadoAuthController extends Controller
{
    public function register(ContratadoRegisterRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $user = User::create([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'tipo' => 'contratado',
                'password' => Hash::make($data['password']),
            ]);

            $contratante = Contratado::create([
                'user_id' => $user->id,
                'nome' => $data['nome'],
                'telefone' => $data['telefone'],
                'email' => $data['email'],
                'data_nascimento' => $data['data_nascimento'],
                'cpf' => $data['cpf'],
                'rg' => $data['rg'],
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'user' => $contratante,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao registrar usuário: ' . $e->getMessage()], 500);
        }
    }

    public function login(ContratadoLoginRequest $request)
    {
        $data = $request->validated();

        $contratado = User::where([
            ['email', '=', $data['email']],
            ['tipo', '=', 'contratado']
        ])->first();

        if (!$contratado || !Hash::check($data['password'], $contratado->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }

        $token = $contratado->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $contratado,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
}
