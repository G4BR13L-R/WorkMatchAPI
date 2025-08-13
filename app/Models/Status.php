<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $fillable = ['descricao'];
    protected $hidden = ['created_at', 'updated_at'];

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class);
    }
}
