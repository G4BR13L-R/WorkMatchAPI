<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    Contratante,
    Contratado,
    Oferta,
    Candidatura,
    Avaliacao
};

class FakeDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Criando contratantes e contratados...');
        $contratantes = Contratante::factory(5)->create();
        $contratados = Contratado::factory(5)->create();

        $this->command->info('Criando ofertas...');
        $ofertas = Oferta::factory(20)->make()->each(function ($oferta) use ($contratantes) {
            $oferta->contratante_id = $contratantes->random()->id;
            $oferta->save();
        });

        $this->command->info('Criando candidaturas sem duplicação...');
        $candidaturasCriadas = collect();
        $totalCandidaturas = 60;

        while ($candidaturasCriadas->count() < $totalCandidaturas) {
            $contratado = $contratados->random();
            $oferta = $ofertas->random();

            $key = $contratado->id . '-' . $oferta->id;

            if ($candidaturasCriadas->contains($key)) {
                continue;
            }

            Candidatura::factory()->create([
                'contratado_id' => $contratado->id,
                'oferta_id' => $oferta->id,
                'status_id' => fake()->randomElement([1, 2, 3]),
            ]);

            $candidaturasCriadas->push($key);
        }

        $this->command->info('Criando avaliações...');
        Avaliacao::factory(35)->create();

        $this->command->info('FakeDataSeeder concluído com sucesso!');
    }
}
