<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'tipo' => $this->faker->randomElement(['contratante', 'contratado']),
            'email_verified_at' => now(),
            'password' => bcrypt('senha123'),
            'remember_token' => Str::random(10),
        ];
    }

    public function contratante()
    {
        return $this->state(fn() => ['tipo' => 'contratante']);
    }

    public function contratado()
    {
        return $this->state(fn() => ['tipo' => 'contratado']);
    }
}
