<?php

namespace App\Services\Contratado;

use App\Models\Contratado;
use App\Models\Endereco;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function getProfile($user)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratado) {
            abort(404, 'Perfil de contratado não encontrado');
        }

        $user->contratado->load('endereco.cidade.estado');

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

            $endereco = Endereco::create(['cidade_id' => $data['cidade_id']]);

            $contratadoData = Arr::except($data, ['cidade_id', 'password', 'password_confirmation']);
            $contratadoData['user_id'] = $user->id;
            $contratadoData['endereco_id'] = $endereco->id;

            $contratado = Contratado::create($contratadoData);

            $token = $user->createToken('auth_token')->plainTextToken;
            $contratado->load('endereco.cidade.estado');

            return [
                'contratado' => $contratado->toArray(),
                'token' => $token,
            ];
        });
    }

    public function updateProfile($user, array $data)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratado) {
            abort(404, 'Perfil de contratado não encontrado');
        }

        $contratado = $user->contratado;

        $enderecoData = Arr::only($data, [
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'cidade_id'
        ]);

        $contratadoData = Arr::except($data, array_keys($enderecoData));

        return DB::transaction(function () use ($contratado, $contratadoData, $enderecoData) {
            $contratado->update($contratadoData);

            if ($contratado->endereco_id) {
                $endereco = Endereco::findOrFail($contratado->endereco_id);
                $endereco->update($enderecoData);
            } else {
                $endereco = Endereco::create($enderecoData);
                $contratadoData['endereco_id'] = $endereco->id;
            }

            $contratado->update($contratadoData);
            $contratado->load('endereco.cidade.estado');

            return $contratado->toArray();
        });
    }

    public function updatePassword($user, array $data)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            abort(422, 'Senha atual incorreta');
        }

        $user->update(['password' => Hash::make($data['new_password'])]);
    }

    public function deleteProfile($user, array $data)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratado) {
            abort(404, 'Perfil de contratado não encontrado');
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            abort(422, 'Senha atual incorreta');
        }

        return DB::transaction(function () use ($user) {
            $user->contratado->delete();
            $user->delete();
        });
    }
}
