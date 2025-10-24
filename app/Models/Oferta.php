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
        'finalizada',
        'endereco_id',
        'contratante_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'endereco_id',
        'contratante_id',
    ];

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class);
    }

    public function contratante(): BelongsTo
    {
        return $this->belongsTo(Contratante::class);
    }

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidatura::class);
    }

    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }
}
