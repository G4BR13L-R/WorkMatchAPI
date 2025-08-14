<?php

namespace App\Services\Auth;

use App\Models\Contratado;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ContratadoAuthService
{
    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'nome' => $data['nome'],
                'email' => $data['email'],
                'tipo' => 'contratado',
                'password' => Hash::make($data['password']),
            ]);

            $contratado = Contratado::create([
                'user_id' => $user->id,
                'nome' => $data['nome'],
                'telefone' => $data['telefone'],
                'email' => $data['email'],
                'data_nascimento' => $data['data_nascimento'],
                'cpf' => $data['cpf'],
                'rg' => $data['rg'],
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'contratado' => $contratado->toArray(),
                'token' => $token,
            ];
        });
    }

    public function login(array $data)
    {
        $user = User::where([
            ['email', '=', $data['email']],
            ['tipo', '=', 'contratado']
        ])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user->toArray(),
            'token' => $token,
        ];
    }
}
