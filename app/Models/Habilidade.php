<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Habilidade extends Model
{
    protected $fillable = ['descricao', 'contratado_id'];

    public function contratado(): BelongsTo
    {
        return $this->belongsTo(Contratado::class);
    }
}
