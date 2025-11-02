<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Endereco;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContratadoFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory()->contratado(),
            'nome' => $this->faker->name(),
            'telefone' => $this->faker->numerify('67########'),
            'email' => $this->faker->unique()->safeEmail(),
            'data_nascimento' => $this->faker->date(),
            'cpf' => $this->faker->unique()->numerify('###########'),
            'rg' => $this->faker->numerify('##############'),
            'formacoes' => $this->faker->sentence(),
            'habilidades' => $this->faker->sentence(),
            'experiencias' => $this->faker->sentence(),
            'endereco_id' => Endereco::factory(),
        ];
    }
}
