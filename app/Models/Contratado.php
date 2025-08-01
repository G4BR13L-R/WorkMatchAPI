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

    public function habilidades(): HasMany
    {
        return $this->hasMany(Habilidade::class);
    }

    public function experiencias(): HasMany
    {
        return $this->hasMany(ExperienciaProfissional::class);
    }

    public function funcoes(): BelongsToMany
    {
        return $this->belongsToMany(Funcao::class, 'contratado_funcoes');
    }

    public function candidaturas(): HasMany
    {
        return $this->hasMany(Candidatura::class);
    }

    public function ofertas(): HasMany
    {
        return $this->hasMany(Oferta::class);
    }
}
