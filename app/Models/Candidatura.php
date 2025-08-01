<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidatura extends Model
{
    protected $fillable = ['contratado_id', 'oferta_id', 'status'];

    public function contratado(): BelongsTo
    {
        return $this->belongsTo(Contratado::class);
    }

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }
}
