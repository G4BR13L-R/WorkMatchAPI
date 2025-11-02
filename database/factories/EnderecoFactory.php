<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EnderecoFactory extends Factory
{
    public function definition()
    {
        return [
            'logradouro' => $this->faker->streetName(),
            'numero' => $this->faker->buildingNumber(),
            'complemento' => $this->faker->secondaryAddress(),
            'bairro' => $this->faker->citySuffix(),
            'cidade_id' => $this->faker->randomElement([5145, 5135]),
        ];
    }
}
