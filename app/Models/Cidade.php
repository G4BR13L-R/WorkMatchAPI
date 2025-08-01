<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cidade extends Model
{
    protected $fillable = ['descricao', 'estado_id'];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

    public function enderecos(): HasMany
    {
        return $this->hasMany(Endereco::class);
    }
}
