<?php

namespace App\Services;

use App\Models\Avaliacao;

class AvaliacaoService
{
    public function getAvaliacao(array $dados)
    {
        $avaliacao = Avaliacao::where([
            ['autor_id', $dados['autor_id']],
            ['autor_tipo', $dados['autor_tipo']],
            ['destinatario_id', $dados['destinatario_id']],
            ['destinatario_tipo', $dados['destinatario_tipo']],
            ['oferta_id', $dados['oferta_id']],
        ])->with([
            'oferta.endereco.cidade.estado',
            'oferta.contratante'
        ])->first();

        return $avaliacao ? $avaliacao->toArray() : null;
    }

    public function registerAvaliacao(array $dados)
    {
        $avaliacao = Avaliacao::create($dados);

        $avaliacao->load(['oferta.endereco.cidade.estado', 'oferta.contratante']);

        return $avaliacao->toArray();
    }

    public function updateAvaliacao(array $dados, int $id)
    {
        $avaliacao = Avaliacao::find($id);

        if (!$avaliacao) {
            abort(404, 'Avaliação não encontrada');
        }

        $avaliacao->update($dados);

        $avaliacao->load(['oferta.endereco.cidade.estado', 'oferta.contratante']);

        return $avaliacao->toArray();
    }

    public function deleteAvaliacao(int $id)
    {
        $avaliacao = Avaliacao::find($id);

        if (!$avaliacao) {
            abort(404, 'Avaliação nao encontrada');
        }

        $avaliacao->delete();
    }
}
