<?php

namespace App\Services;

use App\Models\Candidatura;

class CandidaturaService
{
    public function getCandidaturaById(int $id)
    {
        $candidatura = Candidatura::with([
            'contratado.endereco.cidade.estado',
            'oferta',
            'status'
        ])->find($id);

        if (!$candidatura) {
            abort(404, 'Candidatura nao encontrada');
        }

        return $candidatura->toArray();
    }

    public function changeStatus(int $id, int $statusId)
    {
        $candidatura = Candidatura::with([
            'contratado.endereco.cidade.estado',
            'oferta',
            'status'
        ])->find($id);

        if (!$candidatura) {
            abort(404, 'Candidatura nao encontrada');
        }

        $candidatura->status_id = $statusId;
        $candidatura->save();

        return $candidatura->toArray();
    }
}
