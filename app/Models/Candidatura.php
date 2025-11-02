<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidatura extends Model
{
    use HasFactory;

    protected $fillable = ['contratado_id', 'oferta_id', 'status_id', 'salario'];
    protected $hidden = ['created_at', 'updated_at', 'contratado_id', 'oferta_id', 'status_id'];

    public function contratado(): BelongsTo
    {
        return $this->belongsTo(Contratado::class);
    }

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
