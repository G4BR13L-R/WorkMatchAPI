<?php

namespace App\Services;

use App\Models\Candidatura;
use App\Models\Endereco;
use App\Models\Oferta;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OfertaService
{
    public function listOfertas(?int $cidadeId = null)
    {
        $query = Oferta::with(['endereco.cidade.estado', 'contratante']);

        if (!empty($cidadeId)) {
            $query->whereHas('endereco', function ($query) use ($cidadeId) {
                $query->where('cidade_id', $cidadeId);
            });
        }

        return $query->get()->toArray();
    }

    public function listOfertasByUser($user, ?bool $finalizada)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratante) {
            abort(404, 'Perfil de contratante não encontrado');
        }

        $contratante = $user->contratante;

        $ofertas = Oferta::where('contratante_id', $contratante->id)
            ->with(['endereco.cidade.estado', 'contratante']);

        if ($finalizada !== null) {
            $ofertas->where('finalizada', $finalizada);
        }

        return $ofertas->get()->toArray();
    }

    public function getOfertaById(int $id)
    {
        $oferta = Oferta::where('id', $id)
            ->with(['endereco.cidade.estado', 'contratante'])
            ->first();

        if (!$oferta) {
            abort(404, 'Oferta nao encontrada');
        }

        return $oferta->toArray();
    }

    public function registerOferta($user, array $data)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratante) {
            abort(404, 'Perfil de contratante não encontrado');
        }

        $contratante = $user->contratante;

        $endereco = Arr::only($data, [
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'cidade_id'
        ]);

        $oferta = Arr::except($data, array_keys($endereco));
        $oferta['contratante_id'] = $contratante->id;

        return DB::transaction(function () use ($oferta, $endereco) {
            $endereco = Endereco::create($endereco);
            $oferta['endereco_id'] = $endereco->id;
            $oferta = Oferta::create($oferta);

            $oferta->load(['endereco.cidade.estado', 'contratante', 'candidaturas']);

            return $oferta->toArray();
        });
    }

    public function updateOferta($user, int $id, array $data)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratante) {
            abort(404, 'Perfil de contratante não encontrado');
        }

        $contratante = $user->contratante;

        if (!Oferta::where(['id' => $id, 'contratante_id' => $contratante->id])->exists()) {
            abort(404, 'Oferta nao encontrada');
        }

        $endereco = Arr::only($data, [
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'cidade_id'
        ]);

        $oferta = Arr::except($data, array_keys($endereco));

        return DB::transaction(function () use ($id, $oferta, $endereco) {
            $ofertaModel = Oferta::find($id);
            $ofertaModel->update($oferta);

            $enderecoModel = Endereco::find($ofertaModel->endereco_id);
            $enderecoModel->update($endereco);

            return $ofertaModel->toArray();
        });
    }

    public function deleteOferta($user, int $id)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratante) {
            abort(404, 'Perfil de contratante não encontrado');
        }

        $contratante = $user->contratante;

        $oferta = Oferta::where(['id' => $id, 'contratante_id' => $contratante->id])->first();

        if (!$oferta) {
            abort(404, 'Oferta nao encontrada');
        }

        return DB::transaction(function () use ($oferta) {
            $enderecoId = $oferta->endereco_id;
            $oferta->delete();
            Endereco::where('id', $enderecoId)->delete();
        });
    }

    public function finalizarOferta($user, int $id)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratante) {
            abort(404, 'Perfil de contratante não encontrado');
        }

        $contratante = $user->contratante;

        $oferta = Oferta::where(['id' => $id, 'contratante_id' => $contratante->id])->first();

        if (!$oferta) {
            abort(404, 'Oferta nao encontrada');
        }

        $oferta->finalizada = true;
        $oferta->save();

        return $oferta->toArray();
    }

    public function getCandidatosByOfertaId(int $id)
    {
        $oferta = Oferta::find($id);

        if (!$oferta) {
            abort(404, 'Oferta não encontrada');
        }

        $candidaturas = Candidatura::with([
            'contratado.endereco.cidade.estado',
            'status',
            'oferta.endereco.cidade.estado',
            'oferta.contratante',
        ])->where('oferta_id', $id);

        if ($oferta->finalizada) {
            $candidaturas->where('status_id', 2);
        }

        return $candidaturas->get()->toArray();
    }
}
