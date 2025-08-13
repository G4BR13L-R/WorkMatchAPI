<?php

namespace App\Services\Contratante;

use App\Models\Endereco;
use Exception;
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

    public function updateProfile($user, $data)
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

            return $contratante;
        });
    }

    public function updatePassword($user, $data)
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

        $user->contratante->delete();
        $user->delete();
    }
}
