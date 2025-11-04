<?php

namespace Tests\Unit\Controllers\Contratante;

use App\Http\Controllers\Contratante\CandidaturaController;
use App\Models\Contratado;
use App\Models\Contratante;
use App\Models\Status;
use App\Models\User;
use App\Services\CandidaturaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;
use Exception;

class CandidaturaControllerTest extends TestCase
{
    use RefreshDatabase;

    private CandidaturaController $candidaturaController;
    private $candidaturaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->candidaturaService = Mockery::mock(CandidaturaService::class);
        $this->candidaturaController = new CandidaturaController($this->candidaturaService);

        Status::insert([
            ['id' => 1, 'descricao' => 'Em análise'],
            ['id' => 2, 'descricao' => 'Aprovada'],
            ['id' => 3, 'descricao' => 'Recusada'],
            ['id' => 4, 'descricao' => 'Finalizada']
        ]);
    }

    private function createTestUser($tipo = 'contratante')
    {
        static $count = 0;
        $count++;
        
        return User::create([
            'nome' => 'João Silva',
            'email' => $tipo === 'contratante' 
                ? "joao.contratante{$count}@teste.com" 
                : "pedro.contratado{$count}@teste.com",
            'password' => 'password123',
            'tipo' => $tipo
        ]);
    }

    private function createTestContratante()
    {
        $user = $this->createTestUser('contratante');
        return Contratante::create([
            'user_id' => $user->id,
            'nome' => 'João Silva',
            'email' => 'joao.contratante@teste.com',
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
            'nome' => 'Pedro Santos',
            'email' => 'pedro.contratado@teste.com',
            'cpf' => '12345678901',
            'data_nascimento' => '1990-01-01',
            'telefone' => '11999999999'
        ]);
    }

    public function test_show_sucesso()
    {
        $contratado = $this->createTestContratado();
        $contratante = $this->createTestContratante();

        $candidatura = [
            'id' => 1,
            'contratado' => [
                'id' => $contratado->id,
                'nome' => $contratado->nome,
                'email' => $contratado->email,
                'endereco' => [
                    'cidade' => [
                        'estado' => [
                            'sigla' => 'SP',
                            'descricao' => 'São Paulo'
                        ]
                    ]
                ]
            ],
            'oferta' => [
                'id' => 1,
                'titulo' => 'Vaga teste',
                'contratante' => [
                    'id' => $contratante->id,
                    'nome' => $contratante->nome
                ]
            ],
            'status' => [
                'id' => 1,
                'descricao' => 'Em análise'
            ],
            'avaliacoes' => []
        ];

        $this->candidaturaService->shouldReceive('getCandidaturaById')
            ->once()
            ->with(1)
            ->andReturn($candidatura);

        $response = $this->candidaturaController->show(1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($candidatura, $data);
    }

    public function test_change_status_sucesso()
    {
        $contratado = $this->createTestContratado();
        $contratante = $this->createTestContratante();

        $requestData = [
            'status_id' => 1
        ];

        $candidaturaData = [
            'id' => 1,
            'contratado_id' => $contratado->id,
            'status_id' => 1,
            'status' => [
                'id' => 1,
                'descricao' => 'Em análise'
            ]
        ];

        $this->candidaturaService->shouldReceive('changeStatus')
            ->once()
            ->with(1, 1)
            ->andReturn($candidaturaData);

        $request = Request::create('/api/contratante/candidaturas/1/status', 'PUT', $requestData);
        $response = $this->candidaturaController->changeStatus($request, 1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($candidaturaData, $data);
    }

    public function test_show_nao_encontrada()
    {
        $this->candidaturaService->shouldReceive('getCandidaturaById')
            ->once()
            ->with(999)
            ->andThrow(new \Symfony\Component\HttpKernel\Exception\HttpException(404, 'Candidatura nao encontrada'));

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Candidatura nao encontrada');

        $this->candidaturaController->show(999);
    }

    public function test_change_status_validacao_falha()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->expectExceptionMessage('O campo status é obrigatório');

        $request = Request::create('/api/contratante/candidaturas/1/status', 'PUT', []);
        $this->candidaturaController->changeStatus($request, 1);
    }

    public function test_change_status_status_invalido()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->expectExceptionMessage('O status informado é inválido');

        $requestData = [
            'status_id' => 999
        ];

        $request = Request::create('/api/contratante/candidaturas/1/status', 'PUT', $requestData);
        $this->candidaturaController->changeStatus($request, 1);
    }

    public function test_show_erro_interno()
    {
        $this->candidaturaService->shouldReceive('getCandidaturaById')
            ->once()
            ->with(1)
            ->andThrow(new Exception('Erro interno'));

        $response = $this->candidaturaController->show(1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Não foi possível buscar a candidatura, tente novamente mais tarde', $data['message']);
    }

    public function test_change_status_erro_interno()
    {
        $requestData = ['status_id' => 1];

        $this->candidaturaService->shouldReceive('changeStatus')
            ->once()
            ->with(1, 1)
            ->andThrow(new Exception('Erro interno'));

        $request = Request::create('/api/contratante/candidaturas/1/status', 'PUT', $requestData);
        $response = $this->candidaturaController->changeStatus($request, 1);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Não foi possível atualizar o status da candidatura, tente novamente mais tarde', $data['message']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}