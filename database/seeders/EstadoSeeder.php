<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $estados = [
            [
                "id" => 11,
                "descricao" => "Rondônia",
                "sigla" => "RO",
                "created_at" => $now,
            ],
            [
                "id" => 12,
                "descricao" => "Acre",
                "sigla" => "AC",
                "created_at" => $now,
            ],
            [
                "id" => 13,
                "descricao" => "Amazonas",
                "sigla" => "AM",
                "created_at" => $now,
            ],
            [
                "id" => 14,
                "descricao" => "Roraima",
                "sigla" => "RR",
                "created_at" => $now,
            ],
            [
                "id" => 15,
                "descricao" => "Pará",
                "sigla" => "PA",
                "created_at" => $now,
            ],
            [
                "id" => 16,
                "descricao" => "Amapá",
                "sigla" => "AP",
                "created_at" => $now,
            ],
            [
                "id" => 17,
                "descricao" => "Tocantins",
                "sigla" => "TO",
                "created_at" => $now,
            ],
            [
                "id" => 21,
                "descricao" => "Maranhão",
                "sigla" => "MA",
                "created_at" => $now,
            ],
            [
                "id" => 22,
                "descricao" => "Piauí",
                "sigla" => "PI",
                "created_at" => $now,
            ],
            [
                "id" => 23,
                "descricao" => "Ceará",
                "sigla" => "CE",
                "created_at" => $now,
            ],
            [
                "id" => 24,
                "descricao" => "Rio Grande do Norte",
                "sigla" => "RN",
                "created_at" => $now,
            ],
            [
                "id" => 25,
                "descricao" => "Paraíba",
                "sigla" => "PB",
                "created_at" => $now,
            ],
            [
                "id" => 26,
                "descricao" => "Pernambuco",
                "sigla" => "PE",
                "created_at" => $now,
            ],
            [
                "id" => 27,
                "descricao" => "Alagoas",
                "sigla" => "AL",
                "created_at" => $now,
            ],
            [
                "id" => 28,
                "descricao" => "Sergipe",
                "sigla" => "SE",
                "created_at" => $now,
            ],
            [
                "id" => 29,
                "descricao" => "Bahia",
                "sigla" => "BA",
                "created_at" => $now,
            ],
            [
                "id" => 31,
                "descricao" => "Minas Gerais",
                "sigla" => "MG",
                "created_at" => $now,
            ],
            [
                "id" => 32,
                "descricao" => "Espírito Santo",
                "sigla" => "ES",
                "created_at" => $now,
            ],
            [
                "id" => 33,
                "descricao" => "Rio de Janeiro",
                "sigla" => "RJ",
                "created_at" => $now,
            ],
            [
                "id" => 35,
                "descricao" => "São Paulo",
                "sigla" => "SP",
                "created_at" => $now,
            ],
            [
                "id" => 41,
                "descricao" => "Paraná",
                "sigla" => "PR",
                "created_at" => $now,
            ],
            [
                "id" => 42,
                "descricao" => "Santa Catarina",
                "sigla" => "SC",
                "created_at" => $now,
            ],
            [
                "id" => 43,
                "descricao" => "Rio Grande do Sul",
                "sigla" => "RS",
                "created_at" => $now,
            ],
            [
                "id" => 50,
                "descricao" => "Mato Grosso do Sul",
                "sigla" => "MS",
                "created_at" => $now,
            ],
            [
                "id" => 51,
                "descricao" => "Mato Grosso",
                "sigla" => "MT",
                "created_at" => $now,
            ],
            [
                "id" => 52,
                "descricao" => "Goiás",
                "sigla" => "GO",
                "created_at" => $now,
            ],
            [
                "id" => 53,
                "descricao" => "Distrito Federal",
                "sigla" => "DF",
                "created_at" => $now,
            ],
        ];

        DB::table('estados')->insert($estados);
    }
}
