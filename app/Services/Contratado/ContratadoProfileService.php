<?php

namespace App\Services\Contratado;

use App\Models\Contratado;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ContratadoProfileService
{
    public function getProfile($user)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratado) {
            abort(404, 'Perfil de contratado não encontrado');
        }

        return $user->contratado->toArray();
    }

    public function registerProfile(array $data)
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
}
