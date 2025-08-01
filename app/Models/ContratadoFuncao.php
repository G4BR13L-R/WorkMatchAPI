<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratadoFuncao extends Model
{
    protected $table = 'contratado_funcoes';

    protected $fillable = ['contratado_id', 'funcao_id'];

    public function contratado(): BelongsTo
    {
        return $this->belongsTo(Contratado::class);
    }

    public function funcao(): BelongsTo
    {
        return $this->belongsTo(Funcao::class);
    }
}
