<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Endereco;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContratanteFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory()->contratante(),
            'nome' => $this->faker->company(),
            'telefone' => $this->faker->numerify('67########'),
            'email' => $this->faker->unique()->companyEmail(),
            'cnpj' => $this->faker->unique()->numerify('##############'),
            'razao_social' => $this->faker->company(),
            'nome_fantasia' => $this->faker->companySuffix(),
            'endereco_id' => Endereco::factory(),
        ];
    }
}
