<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'autor_id',
        'autor_tipo',
        'autor_nome',
        'destinatario_id',
        'destinatario_tipo',
        'destinatario_nome',
        'oferta_id',
        'nota',
        'comentario'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }
}
