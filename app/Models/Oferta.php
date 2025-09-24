<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Oferta extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'salario',
        'data_inicio',
        'data_fim',
        'endereco_id',
        'contratante_id',
        'contratado_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'endereco_id',
        'contratante_id',
        'contratado_id'
    ];

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class);
    }

    public function contratante(): BelongsTo
    {
        return $this->belongsTo(Contratante::class);
    }

    public function contratado(): BelongsTo
    {
        return $this->belongsTo(Contratado::class);
    }

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidatura::class);
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    public function toArray()
    {
        $data = parent::toArray();

        $data = array_merge($data, [
            'endereco' => $this->endereco ? $this->endereco->toArray() : null,
            'contratante' => $this->contratante ? $this->contratante->toArray() : null,
            'contratado' => $this->contratado ? $this->contratado->toArray() : null,
        ]);

        return $data;
    }
}
