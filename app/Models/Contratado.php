<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contratado extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
        'telefone',
        'email',
        'data_nascimento',
        'cpf',
        'rg',
        'formacoes',
        'habilidades',
        'experiencias',
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

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidatura::class);
    }
}
