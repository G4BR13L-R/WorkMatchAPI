<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $fillable = ['descricao'];

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class);
    }
}
