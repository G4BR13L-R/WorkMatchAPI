<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContratanteLoginRequest;
use App\Http\Requests\ContratanteRegisterRequest;
use App\Models\Contratante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ContratanteAuthController extends Controller
{
    public function register(ContratanteRegisterRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $user = User::create([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'tipo' => 'contratante',
                'password' => Hash::make($data['password']),
            ]);

            $contratante = Contratante::create([
                'user_id' => $user->id,
                'nome' => $data['nome'],
                'telefone' => $data['telefone'],
                'email' => $data['email'],
                'cnpj' => $data['cnpj'],
                'razao_social' => $data['razao_social'],
                'nome_fantasia' => $data['nome_fantasia'],
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

    public function login(ContratanteLoginRequest $request)
    {
        $data = $request->validated();

        $contratante = User::where([
            ['email', '=', $data['email']],
            ['tipo', '=', 'contratante']
        ])->first();

        if (!$contratante || !Hash::check($data['password'], $contratante->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }

        $token = $contratante->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $contratante,
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
