<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contratante extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
        'telefone',
        'email',
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'endereco_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id',
        'endereco_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class);
    }

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class);
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['endereco'] = $this->endereco ? $this->endereco->toArray() : null;
        return $data;
    }
}
