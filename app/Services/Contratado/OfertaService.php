<?php

namespace App\Services\Contratado;

use App\Models\Avaliacao;
use App\Models\Oferta;

class OfertaService
{
    public function listOfertas($user, ?bool $finalizada, ?int $cidadeId = null)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratado) {
            abort(404, 'Perfil de contratado não encontrado');
        }

        $contratado = $user->contratado;
        $endereco = $contratado?->endereco;

        if (empty($endereco?->cidade_id)) {
            abort(400, 'Perfil sem cidade cadastrada');
        }

        if (empty($cidadeId)) {
            $cidadeId = $endereco->cidade_id;
        }

        $query = Oferta::with([
            'endereco.cidade.estado',
            'contratante',
            'candidaturas' => function ($q) use ($contratado) {
                $q->where('contratado_id', $contratado->id)->with([
                    'contratado.endereco.cidade.estado',
                    'status',
                    'oferta.endereco.cidade.estado',
                    'oferta.contratante',
                ]);
            }
        ])->when($finalizada !== null, function ($q) use ($finalizada, $contratado) {
            $q->where('finalizada', $finalizada);

            if ($finalizada === true) {
                $q->whereHas('candidaturas', function ($sub) use ($contratado) {
                    $sub->where('contratado_id', $contratado->id);
                });
            }
        })->when(!empty($cidadeId), function ($q) use ($cidadeId) {
            $q->whereHas('endereco', function ($sub) use ($cidadeId) {
                $sub->where('cidade_id', $cidadeId);
            });
        });

        return $query->get()->toArray();
    }

    public function getOfertaById($user, int $id)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratado) {
            abort(404, 'Perfil de contratado não encontrado');
        }

        $contratado = $user->contratado;

        $oferta = Oferta::where('id', $id)->with([
            'endereco.cidade.estado',
            'contratante',
            'candidaturas' => function ($query) use ($contratado) {
                $query->where('contratado_id', $contratado->id)->with([
                    'contratado.endereco.cidade.estado',
                    'status',
                    'oferta.endereco.cidade.estado',
                    'oferta.contratante',
                ]);
            }
        ])->first();

        if (!$oferta) {
            abort(404, 'Oferta não encontrada');
        }

        $oferta = array_merge($oferta->toArray(), [
            'avaliacoes' => Avaliacao::where([
                'destinatario_id' => $oferta->contratante_id,
                'destinatario_tipo' => 'contratante',
            ])->with(
                ['oferta.endereco.cidade.estado', 'oferta.contratante']
            )->orderBy('created_at', 'desc')->get()->toArray()
        ]);

        return $oferta;
    }

    public function registerCandidatura($user, array $data)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratado) {
            abort(404, 'Perfil de contratado não encontrado');
        }

        $contratado = $user->contratado;

        $candidatura = $contratado->candidaturas()->create([
            'oferta_id' => $data['oferta_id'],
            'salario' => $data['salario'] ?? null,
            'status_id' => 1, // Status "Em análise"
        ]);

        return $candidatura->load([
            'contratado.endereco.cidade.estado',
            'status',
            'oferta.endereco.cidade.estado',
            'oferta.contratante',
        ])->toArray();
    }

    public function deleteCandidatura($user, int $id)
    {
        if (!$user) {
            abort(404, 'Usuário não encontrado');
        }

        if (!$user->contratado) {
            abort(404, 'Perfil de contratado não encontrado');
        }

        $contratado = $user->contratado;

        $candidatura = $contratado->candidaturas()->where('id', $id)->first();

        if (!$candidatura) {
            abort(404, 'Candidatura nao encontrada');
        }

        $candidatura->delete();
    }
}
