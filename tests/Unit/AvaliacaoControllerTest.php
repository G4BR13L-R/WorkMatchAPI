<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\AvaliacaoController;
use App\Http\Requests\AvaliacaoRequest;
use App\Models\Cidade;
use App\Models\Contratado;
use App\Models\Contratante;
use App\Models\Endereco;
use App\Models\Estado;
use App\Models\Oferta;
use App\Models\Status;
use App\Services\AvaliacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;
use Exception;

class AvaliacaoControllerTest extends TestCase
{
    use RefreshDatabase;

    private AvaliacaoController $avaliacaoController;
    private $avaliacaoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->avaliacaoService = Mockery::mock(AvaliacaoService::class);
        $this->avaliacaoController = new AvaliacaoController($this->avaliacaoService);

        Status::create([
            'id' => 1,
            'descricao' => 'Ativa'
        ]);
    }

    private function createTestUser($tipo = 'contratante')
    {
        return \App\Models\User::create([
            'nome' => 'Teste',
            'email' => "teste_$tipo@teste.com",
            'password' => 'password123',
            'tipo' => $tipo
        ]);
    }

    private function createTestLocations()
    {
        $estado = Estado::create([
            'sigla' => 'SP',
            'descricao' => 'São Paulo'
        ]);

        $cidade = Cidade::create([
            'estado_id' => $estado->id,
            'descricao' => 'São Paulo'
        ]);

        $endereco = Endereco::create([
            'logradouro' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cep' => '12345678',
            'cidade_id' => $cidade->id,
            'complemento' => 'Apto 1'
        ]);

        return $endereco;
    }

    private function createTestContratante()
    {
        $user = $this->createTestUser('contratante');
        return Contratante::create([
            'user_id' => $user->id,
            'nome' => 'Empresa Teste',
            'email' => 'empresa@teste.com',
            'cnpj' => '12345678901234',
            'razao_social' => 'Empresa Teste LTDA',
            'nome_fantasia' => 'Empresa Teste',
            'telefone' => '11999999999'
        ]);
    }

    private function createTestContratado()
    {
        $user = $this->createTestUser('contratado');
        return Contratado::create([
            'user_id' => $user->id,
            'nome' => 'João Silva',
            'email' => 'joao@teste.com',
            'cpf' => '12345678901',
            'data_nascimento' => '1990-01-01',
            'telefone' => '11999999999'
        ]);
    }

    private function createTestOferta($contratanteId)
    {
        $endereco = $this->createTestLocations();

        return Oferta::create([
            'contratante_id' => $contratanteId,
            'titulo' => 'Vaga teste',
            'descricao' => 'Descrição teste',
            'valor' => 1000,
            'prazo_estimado' => '2023-12-31',
            'data_inicio' => '2023-11-01',
            'data_fim' => '2023-12-31',
            'endereco_id' => $endereco->id,
            'status_id' => 1
        ]);
    }

    public function test_show_sucesso()
    {
        $contratante = $this->createTestContratante();
        $contratado = $this->createTestContratado();
        $oferta = $this->createTestOferta($contratante->id);

        $requestData = [
            'autor_id' => $contratante->id,
            'autor_tipo' => 'contratante',
            'destinatario_id' => $contratado->id,
            'destinatario_tipo' => 'contratado',
            'oferta_id' => $oferta->id
        ];

        $avaliacaoData = [
            'id' => 1,
            'autor_id' => $contratante->id,
            'autor_tipo' => 'contratante',
            'autor_nome' => 'Empresa Teste',
            'destinatario_id' => $contratado->id,
            'destinatario_tipo' => 'contratado',
            'destinatario_nome' => 'João Silva',
            'oferta_id' => $oferta->id,
            'nota' => 5,
            'comentario' => 'Ótimo profissional'
        ];

        $this->avaliacaoService->shouldReceive('getAvaliacao')
            ->once()
            ->with($requestData)
            ->andReturn($avaliacaoData);

        $request = Request::create('/api/avaliacao', 'GET', $requestData);

        $response = $this->avaliacaoController->show($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($avaliacaoData, $data);
    }

    public function test_store_sucesso()
    {
        $contratante = $this->createTestContratante();
        $contratado = $this->createTestContratado();
        $oferta = $this->createTestOferta($contratante->id);

        $requestData = [
            'autor_id' => $contratante->id,
            'autor_tipo' => 'contratante',
            'destinatario_id' => $contratado->id,
            'destinatario_tipo' => 'contratado',
            'oferta_id' => $oferta->id,
            'nota' => 5,
            'comentario' => 'Ótimo profissional'
        ];

        $avaliacaoData = array_merge($requestData, [
            'id' => 1,
            'autor_nome' => 'Empresa Teste',
            'destinatario_nome' => 'João Silva'
        ]);

        $this->avaliacaoService->shouldReceive('registerAvaliacao')
            ->once()
            ->with(Mockery::subset($requestData))
            ->andReturn($avaliacaoData);

        $request = Mockery::mock(AvaliacaoRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);

        $response = $this->avaliacaoController->store($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($avaliacaoData, $data);
    }

    public function test_update_sucesso()
    {
        $contratante = $this->createTestContratante();
        $contratado = $this->createTestContratado();
        $oferta = $this->createTestOferta($contratante->id);

        $requestData = [
            'autor_id' => $contratante->id,
            'autor_tipo' => 'contratante',
            'destinatario_id' => $contratado->id,
            'destinatario_tipo' => 'contratado',
            'oferta_id' => $oferta->id,
            'nota' => 4,
            'comentario' => 'Bom profissional'
        ];

        $avaliacaoData = array_merge($requestData, [
            'id' => 1,
            'autor_nome' => 'Empresa Teste',
            'destinatario_nome' => 'João Silva'
        ]);

        $this->avaliacaoService->shouldReceive('updateAvaliacao')
            ->once()
            ->with(Mockery::subset($requestData), 1)
            ->andReturn($avaliacaoData);

        $request = Mockery::mock(AvaliacaoRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);

        $response = $this->avaliacaoController->update($request, 1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($avaliacaoData, $data);
    }

    public function test_destroy_sucesso()
    {
        $this->avaliacaoService->shouldReceive('deleteAvaliacao')
            ->once()
            ->with(1)
            ->andReturn(null);

        $response = $this->avaliacaoController->destroy(1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Avaliação deletada com sucesso', $data['message']);
    }

    public function test_show_validacao_falha()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $requestData = [
            'autor_id' => 'invalid',
            'autor_tipo' => 'invalid',
            'destinatario_id' => 'invalid',
            'destinatario_tipo' => 'invalid',
            'oferta_id' => 'invalid'
        ];

        $request = Request::create('/api/avaliacao', 'GET', $requestData);
        $this->avaliacaoController->show($request);
    }

    public function test_store_erro_interno()
    {
        $contratante = $this->createTestContratante();
        $contratado = $this->createTestContratado();
        $oferta = $this->createTestOferta($contratante->id);

        $requestData = [
            'autor_id' => $contratante->id,
            'autor_tipo' => 'contratante',
            'destinatario_id' => $contratado->id,
            'destinatario_tipo' => 'contratado',
            'oferta_id' => $oferta->id,
            'nota' => 5,
            'comentario' => 'Ótimo profissional'
        ];

        $this->avaliacaoService->shouldReceive('registerAvaliacao')
            ->once()
            ->with(Mockery::subset($requestData))
            ->andThrow(new Exception('Erro interno'));

        $request = Mockery::mock(AvaliacaoRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);

        $response = $this->avaliacaoController->store($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Não foi possível registrar a avaliação, tente novamente mais tarde', $data['message']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
