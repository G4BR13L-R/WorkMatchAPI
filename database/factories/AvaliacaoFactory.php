<?php

namespace Database\Factories;

use App\Models\Oferta;
use App\Models\Contratado;
use App\Models\Contratante;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvaliacaoFactory extends Factory
{
    public function definition()
    {
        $autorTipo = $this->faker->randomElement(['contratante', 'contratado']);
        $autor = $autorTipo === 'contratante'
            ? Contratante::factory()->create()
            : Contratado::factory()->create();

        $destTipo = $autorTipo === 'contratante' ? 'contratado' : 'contratante';
        $dest = $destTipo === 'contratante'
            ? Contratante::factory()->create()
            : Contratado::factory()->create();

        return [
            'autor_id' => $autor->id,
            'autor_tipo' => $autorTipo,
            'autor_nome' => $autor->nome,
            'destinatario_id' => $dest->id,
            'destinatario_tipo' => $destTipo,
            'destinatario_nome' => $dest->nome,
            'oferta_id' => Oferta::factory(),
            'nota' => $this->faker->numberBetween(1, 5),
            'comentario' => $this->faker->sentence(),
        ];
    }
}
 