<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Endereco extends Model
{
    protected $fillable = ['logradouro', 'numero', 'complemento', 'bairro', 'cidade_id'];
    protected $hidden = ['created_at', 'updated_at', 'cidade_id'];

    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class);
    }

    public function contratados(): HasMany
    {
        return $this->hasMany(Contratado::class);
    }

    public function contratantes(): HasMany
    {
        return $this->hasMany(Contratante::class);
    }

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class);
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['cidade'] = $this->cidade ? $this->cidade->toArray() : null;
        return $data;
    }
}
