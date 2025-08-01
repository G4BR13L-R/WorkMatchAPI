<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends Model
{
    protected $fillable = [
        'autor_id',
        'autor_tipo',
        'destinatario_id',
        'destinatario_tipo',
        'oferta_id',
        'nota',
        'comentario'
    ];

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }
}
