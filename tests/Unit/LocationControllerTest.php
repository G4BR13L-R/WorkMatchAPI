<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\LocationController;
use App\Models\Estado;
use App\Models\Cidade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase;

    private LocationController $locationController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locationController = new LocationController();
    }

    public function test_estados_sucesso()
    {
        $estado1 = Estado::create([
            'sigla' => 'SP',
            'descricao' => 'São Paulo'
        ]);

        $estado2 = Estado::create([
            'sigla' => 'RJ',
            'descricao' => 'Rio de Janeiro'
        ]);

        $response = $this->locationController->estados();
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(2, $data);
        $this->assertEquals('Rio de Janeiro', $data[0]['descricao']);
        $this->assertEquals('São Paulo', $data[1]['descricao']);
    }

    public function test_estados_vazio()
    {
        $response = $this->locationController->estados();
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Nenhum estado encontrado', $data['message']);
    }

    public function test_cidades_sucesso()
    {
        $estado = Estado::create([
            'sigla' => 'SP',
            'descricao' => 'São Paulo'
        ]);

        Cidade::create([
            'estado_id' => $estado->id,
            'descricao' => 'São Paulo'
        ]);

        Cidade::create([
            'estado_id' => $estado->id,
            'descricao' => 'Campinas'
        ]);

        $response = $this->locationController->cidades($estado->id);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(2, $data);
        $this->assertEquals('Campinas', $data[0]['descricao']);
        $this->assertEquals('São Paulo', $data[1]['descricao']);
        $this->assertEquals($estado->descricao, $data[0]['estado']['descricao']);
    }

    public function test_cidades_estado_nao_encontrado()
    {
        $response = $this->locationController->cidades(999);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Erro ao buscar cidades', $data['message']);
    }

    public function test_cidades_estado_sem_cidades()
    {
        $estado = Estado::create([
            'sigla' => 'SP',
            'descricao' => 'São Paulo'
        ]);

        $response = $this->locationController->cidades($estado->id);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Nenhuma cidade encontrada', $data['message']);
    }

    public function test_cidades_by_name_sucesso()
    {
        $estado = Estado::create([
            'sigla' => 'SP',
            'descricao' => 'São Paulo'
        ]);

        Cidade::create([
            'estado_id' => $estado->id,
            'descricao' => 'São Paulo'
        ]);

        Cidade::create([
            'estado_id' => $estado->id,
            'descricao' => 'São José dos Campos'
        ]);

        $response = $this->locationController->cidadesByName('São');
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(2, $data);
        $this->assertEquals('São José dos Campos', $data[0]['descricao']);
        $this->assertEquals('São Paulo', $data[1]['descricao']);
        $this->assertEquals($estado->descricao, $data[0]['estado']['descricao']);
    }

    public function test_cidades_by_name_nao_encontrada()
    {
        $response = $this->locationController->cidadesByName('CidadeInexistente');
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Nenhuma cidade encontrada', $data['message']);
    }
}