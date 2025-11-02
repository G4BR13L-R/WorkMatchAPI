<?php

namespace Database\Factories;

use App\Models\Endereco;
use App\Models\Contratante;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfertaFactory extends Factory
{
    public function definition()
    {
        return [
            'titulo' => $this->faker->jobTitle(),
            'descricao' => $this->faker->paragraph(),
            'salario' => $this->faker->randomFloat(2, 1200, 10000),
            'data_inicio' => now(),
            'data_fim' => $this->faker->dateTimeBetween('+5 days', '+60 days'),
            'finalizada' => $this->faker->boolean(40),
            'endereco_id' => Endereco::factory(),
            'contratante_id' => Contratante::factory(),
        ];
    }
}
