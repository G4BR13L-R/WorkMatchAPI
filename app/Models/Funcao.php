<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Funcao extends Model
{
    protected $fillable = ['descricao'];

    public function contratados(): BelongsToMany
    {
        return $this->belongsToMany(Contratado::class, 'contratado_funcoes');
    }
}
