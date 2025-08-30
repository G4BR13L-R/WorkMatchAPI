<?php

namespace App\Services\Contratante;

use App\Models\Contratante;
use App\Models\Endereco;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ContratanteProfileService
{
    public function getProfile($user)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratante) {
            abort(404, 'Perfil de contratante não encontrado');
        }

        return $user->contratante->toArray();
    }

    public function registerProfile(array $data)
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
                'contratante' => $contratante->toArray(),
                'token' => $token,
            ];
        });
    }

    public function updateProfile($user, array $data)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratante) {
            abort(404, 'Perfil de contratante não encontrado');
        }

        $contratante = $user->contratante;

        // Extrai dados do endereço
        $enderecoData = Arr::only($data, [
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'cidade_id'
        ]);

        // Remove os campos de endereço dos dados principais
        $contratanteData = Arr::except($data, array_keys($enderecoData));

        return DB::transaction(function () use ($contratante, $contratanteData, $enderecoData) {
            // Cria ou atualiza endereço
            if ($contratante->endereco_id) {
                $endereco = Endereco::findOrFail($contratante->endereco_id);
                $endereco->update($enderecoData);
            } else {
                $endereco = Endereco::create($enderecoData);
                $contratanteData['endereco_id'] = $endereco->id;
            }

            $contratante->update($contratanteData);

            return $contratante->toArray();
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

    public function deleteProfile($user)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratante) {
            abort(404, 'Perfil de contratante não encontrado');
        }

        DB::transaction(function () use ($user) {
            $user->contratante->delete();
            $user->delete();
        });
    }
}
