<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status')->insert([
            ['id' => 1, 'descricao' => 'Inscrito', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'descricao' => 'Em análise', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'descricao' => 'Contratado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'descricao' => 'Reprovado', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
