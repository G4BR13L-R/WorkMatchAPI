<?php

namespace Tests\Unit\Controllers\Contratado;

use App\Http\Controllers\Contratado\OfertaController;
use App\Models\Contratado;
use App\Models\Status;
use App\Models\User;
use App\Services\Contratado\OfertaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;
use Exception;

class OfertaContratadoControllerTest extends TestCase
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
            'descricao' => 'Ativa'
        ]);
    }

    private function createTestUser()
    {
        return User::create([
            'nome' => 'João Silva',
            'email' => 'joao@teste.com',
            'password' => 'password123',
            'tipo' => 'contratado'
        ]);
    }

    private function createTestContratado($userId)
    {
        return Contratado::create([
            'user_id' => $userId,
            'nome' => 'João Silva',
            'email' => 'joao@teste.com',
            'cpf' => '12345678901',
            'data_nascimento' => '1990-01-01',
            'telefone' => '11999999999'
        ]);
    }

    public function test_index_sucesso()
    {
        $user = $this->createTestUser();
        $this->createTestContratado($user->id);

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

        $this->ofertaService->shouldReceive('listOfertas')
            ->once()
            ->with($user, false, null)
            ->andReturn($ofertas);

        $request = Request::create('/api/contratado/ofertas', 'GET');
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
        $user = $this->createTestUser();
        $this->createTestContratado($user->id);

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
            ->with($user, 1)
            ->andReturn($oferta);

        $request = Request::create('/api/contratado/ofertas/1', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->ofertaController->show($request, 1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($oferta, $data);
    }

    public function test_store_sucesso()
    {
        $user = $this->createTestUser();
        $this->createTestContratado($user->id);

        $requestData = [
            'oferta_id' => 1,
            'salario' => 1000
        ];

        $candidaturaData = array_merge($requestData, [
            'id' => 1,
            'contratado' => [
                'nome' => 'João Silva',
                'email' => 'joao@teste.com'
            ],
            'status' => [
                'id' => 1,
                'descricao' => 'Em análise'
            ]
        ]);

        $this->ofertaService->shouldReceive('registerCandidatura')
            ->once()
            ->with($user, $requestData)
            ->andReturn($candidaturaData);

        $request = Request::create('/api/contratado/ofertas', 'POST', $requestData);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->ofertaController->store($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($candidaturaData, $data);
    }

    public function test_destroy_sucesso()
    {
        $user = $this->createTestUser();
        $this->createTestContratado($user->id);

        $this->ofertaService->shouldReceive('deleteCandidatura')
            ->once()
            ->with($user, 1)
            ->andReturn(null);

        $request = Request::create('/api/contratado/ofertas/1', 'DELETE');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->ofertaController->destroy($request, 1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Candidatura excluído com sucesso!', $data['message']);
    }

    public function test_store_validacao_falha()
    {
        $user = $this->createTestUser();

        $this->ofertaService->shouldReceive('registerCandidatura')
            ->never();

        $request = Request::create('/api/contratado/ofertas', 'POST', []);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->ofertaController->store($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Não foi possível criar a candidatura, tente novamente mais tarde', $data['message']);
    }

    public function test_store_erro_interno()
    {
        $user = $this->createTestUser();
        $this->createTestContratado($user->id);

        $requestData = [
            'oferta_id' => 1,
            'salario' => 1000
        ];

        $this->ofertaService->shouldReceive('registerCandidatura')
            ->once()
            ->with($user, $requestData)
            ->andThrow(new Exception('Erro interno'));

        $request = Request::create('/api/contratado/ofertas', 'POST', $requestData);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->ofertaController->store($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Não foi possível criar a candidatura, tente novamente mais tarde', $data['message']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
