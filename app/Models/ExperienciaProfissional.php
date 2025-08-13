<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienciaProfissional extends Model
{
    protected $fillable = [
        'cargo',
        'empresa',
        'data_inicio',
        'data_fim',
        'descricao',
        'contratado_id'
    ];

    protected $hidden = ['created_at', 'updated_at', 'contratado_id'];

    public function contratado(): BelongsTo
    {
        return $this->belongsTo(Contratado::class);
    }
}
