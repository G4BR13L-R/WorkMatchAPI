<?php

namespace Tests\Unit\Controllers\Contratante;

use App\Http\Controllers\Contratante\OfertaController;
use App\Http\Requests\OfertaRequest;
use App\Models\Cidade;
use App\Models\Contratante;
use App\Models\Endereco;
use App\Models\Estado;
use App\Models\Status;
use App\Models\User;
use App\Services\Contratante\OfertaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;
use Exception;

class OfertaContratanteControllerTest extends TestCase
{
    use RefreshDatabase;

    private OfertaController $ofertaController;
    private $ofertaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ofertaService = Mockery::mock(OfertaService::class);
        $this->ofertaController = new OfertaController($this->ofertaService);

        Status::create([
            'id' => 1,
            'descricao' => 'Em análise'
        ]);
    }

    private function createTestUser()
    {
        return User::create([
            'nome' => 'João Silva',
            'email' => 'joao@teste.com',
            'password' => 'password123',
            'tipo' => 'contratante'
        ]);
    }

    private function createTestContratante($userId)
    {
        return Contratante::create([
            'user_id' => $userId,
            'nome' => 'João Silva',
            'email' => 'joao@teste.com',
            'cnpj' => '12345678901234',
            'razao_social' => 'Empresa Teste LTDA',
            'nome_fantasia' => 'Empresa Teste',
            'telefone' => '11999999999'
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

        return Endereco::create([
            'logradouro' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade_id' => $cidade->id,
            'complemento' => 'Apto 1'
        ]);
    }

    public function test_index_sucesso()
    {
        $user = $this->createTestUser();
        $contratante = $this->createTestContratante($user->id);

        $ofertas = [
            [
                'id' => 1,
                'titulo' => 'Vaga teste',
                'descricao' => 'Descrição teste',
                'valor' => 1000,
                'prazo_estimado' => '2023-12-31',
                'data_inicio' => '2023-11-01',
                'data_fim' => '2023-12-31',
                'status_id' => 1,
                'contratante' => [
                    'id' => $contratante->id,
                    'nome' => $contratante->nome
                ],
                'endereco' => [
                    'cidade' => [
                        'estado' => [
                            'sigla' => 'SP',
                            'descricao' => 'São Paulo'
                        ]
                    ]
                ]
            ]
        ];

        $this->ofertaService->shouldReceive('getOfertas')
            ->once()
            ->with($user, false)
            ->andReturn($ofertas);

        $request = Request::create('/api/contratante/ofertas', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->ofertaController->index($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($ofertas, $data);
    }

    public function test_show_sucesso()
    {
        $oferta = [
            'id' => 1,
            'titulo' => 'Vaga teste',
            'descricao' => 'Descrição teste',
            'valor' => 1000,
            'prazo_estimado' => '2023-12-31',
            'data_inicio' => '2023-11-01',
            'data_fim' => '2023-12-31',
            'status_id' => 1,
            'endereco' => [
                'cidade' => [
                    'estado' => [
                        'sigla' => 'SP',
                        'descricao' => 'São Paulo'
                    ]
                ]
            ],
            'avaliacoes' => []
        ];

        $this->ofertaService->shouldReceive('getOfertaById')
            ->once()
            ->with(1)
            ->andReturn($oferta);

        $response = $this->ofertaController->show(1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($oferta, $data);
    }

    public function test_store_sucesso()
    {
        $user = $this->createTestUser();
        $contratante = $this->createTestContratante($user->id);
        $endereco = $this->createTestLocations();

        $requestData = [
            'titulo' => 'Vaga teste',
            'descricao' => 'Descrição teste',
            'valor' => 1000,
            'prazo_estimado' => '2023-12-31',
            'data_inicio' => '2023-11-01',
            'data_fim' => '2023-12-31',
            'logradouro' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade_id' => $endereco->cidade_id,
            'complemento' => 'Apto 1'
        ];

        $ofertaData = array_merge($requestData, [
            'id' => 1,
            'contratante_id' => $contratante->id,
            'status_id' => 1
        ]);

        $this->ofertaService->shouldReceive('registerOferta')
            ->once()
            ->with($user, $requestData)
            ->andReturn($ofertaData);

        $request = Mockery::mock(OfertaRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);
        $request->shouldReceive('user')->andReturn($user);

        $response = $this->ofertaController->store($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($ofertaData, $data);
    }

    public function test_update_sucesso()
    {
        $user = $this->createTestUser();
        $contratante = $this->createTestContratante($user->id);
        $endereco = $this->createTestLocations();

        $requestData = [
            'titulo' => 'Vaga teste atualizada',
            'descricao' => 'Descrição teste atualizada',
            'valor' => 2000,
            'prazo_estimado' => '2024-12-31',
            'data_inicio' => '2024-01-01',
            'data_fim' => '2024-12-31',
            'logradouro' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade_id' => $endereco->cidade_id,
            'complemento' => 'Apto 1'
        ];

        $ofertaData = array_merge($requestData, [
            'id' => 1,
            'contratante_id' => $contratante->id,
            'status_id' => 1
        ]);

        $this->ofertaService->shouldReceive('updateOferta')
            ->once()
            ->with($user, 1, $requestData)
            ->andReturn($ofertaData);

        $request = Mockery::mock(OfertaRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);
        $request->shouldReceive('user')->andReturn($user);

        $response = $this->ofertaController->update($request, 1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($ofertaData, $data);
    }

    public function test_destroy_sucesso()
    {
        $user = $this->createTestUser();
        $contratante = $this->createTestContratante($user->id);

        $this->ofertaService->shouldReceive('deleteOferta')
            ->once()
            ->with($user, 1)
            ->andReturn(null);

        $request = Request::create('/api/contratante/ofertas/1', 'DELETE');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->ofertaController->destroy($request, 1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Oferta deletada com sucesso!', $data['message']);
    }

    public function test_finalizar_oferta_sucesso()
    {
        $user = $this->createTestUser();
        $contratante = $this->createTestContratante($user->id);

        $ofertaData = [
            'id' => 1,
            'titulo' => 'Vaga teste',
            'finalizada' => true,
            'contratante_id' => $contratante->id
        ];

        $this->ofertaService->shouldReceive('finalizarOferta')
            ->once()
            ->with($user, 1)
            ->andReturn($ofertaData);

        $request = Request::create('/api/contratante/ofertas/1/finalizar', 'PUT');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->ofertaController->finalizarOferta($request, 1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($ofertaData, $data);
    }

    public function test_candidatos_oferta_sucesso()
    {
        $candidatos = [
            [
                'id' => 1,
                'contratado' => [
                    'nome' => 'Candidato Teste',
                    'email' => 'candidato@teste.com'
                ],
                'status' => [
                    'id' => 1,
                    'descricao' => 'Em análise'
                ]
            ]
        ];

        $this->ofertaService->shouldReceive('getCandidatosByOfertaId')
            ->once()
            ->with(1)
            ->andReturn($candidatos);

        $response = $this->ofertaController->candidatosOferta(1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($candidatos, $data);
    }

    public function test_store_validacao_falha()
    {
        $user = $this->createTestUser();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $validator = Mockery::mock(\Illuminate\Contracts\Validation\Validator::class);
        $validator->shouldReceive('errors->all')->andReturn(['O campo título é obrigatório.']);
        $validator->shouldReceive('getTranslator->get')->andReturn('O campo título é obrigatório.');

        $request = Mockery::mock(OfertaRequest::class);
        $request->shouldReceive('validated')
            ->once()
            ->andThrow(\Illuminate\Validation\ValidationException::withMessages([
                'titulo' => ['O campo título é obrigatório.']
            ]));
        $request->shouldReceive('user')->andReturn($user);

        $this->ofertaController->store($request);
    }

    public function test_store_erro_interno()
    {
        $user = $this->createTestUser();
        $contratante = $this->createTestContratante($user->id);
        $endereco = $this->createTestLocations();

        $requestData = [
            'titulo' => 'Vaga teste',
            'descricao' => 'Descrição teste',
            'valor' => 1000,
            'prazo_estimado' => '2023-12-31',
            'data_inicio' => '2023-11-01',
            'data_fim' => '2023-12-31',
            'logradouro' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade_id' => $endereco->cidade_id,
            'complemento' => 'Apto 1'
        ];

        $this->ofertaService->shouldReceive('registerOferta')
            ->once()
            ->with($user, $requestData)
            ->andThrow(new Exception('Erro interno'));

        $request = Mockery::mock(OfertaRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($requestData);
        $request->shouldReceive('user')->andReturn($user);

        $response = $this->ofertaController->store($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Não foi possível criar a oferta, tente novamente mais tarde', $data['message']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
