<?php

namespace App\Services\Auth;

use App\Models\Contratante;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ContratanteAuthService
{
    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
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

            return [
                'contratante' => $contratante,
                'token' => $token,
            ];
        });
    }

    public function login(array $data)
    {
        $user = User::where([
            ['email', '=', $data['email']],
            ['tipo', '=', 'contratante']
        ])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
