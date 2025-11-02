<?php

namespace Database\Factories;

use App\Models\Contratado;
use App\Models\Oferta;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidaturaFactory extends Factory
{
    public function definition()
    {
        return [
            'contratado_id' => Contratado::factory(),
            'oferta_id' => Oferta::factory(),
            'status_id' => $this->faker->randomElement([1, 2, 3]),
            'salario' => $this->faker->randomFloat(2, 1200, 10000),
        ];
    }
}
