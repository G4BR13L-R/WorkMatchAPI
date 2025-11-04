<?php

namespace Tests\Unit\Controllers\Contratado;

use App\Http\Controllers\Contratado\ContratadoController;
use App\Models\User;
use App\Models\Contratado;
use App\Services\Contratado\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Http\Requests\ContratadoRegisterRequest;
use App\Http\Requests\ContratadoUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\DeleteAccountRequest;

class ContratadoControllerTest extends TestCase
{
    use RefreshDatabase;

    private ContratadoController $contratadoController;
    private $profileService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->profileService = Mockery::mock(ProfileService::class);
        $this->contratadoController = new ContratadoController($this->profileService);
    }

    public function test_show_sucesso()
    {
        $user = User::create([
            'nome' => 'Teste Contratado',
            'email' => 'contratado@test.com',
            'tipo' => 'contratado',
            'password' => Hash::make('123456'),
        ]);

        $contratado = Contratado::create([
            'user_id' => $user->id,
            'nome' => 'Teste Contratado',
            'telefone' => '11999999999',
            'email' => 'contratado@test.com',
            'cpf' => '12345678901',
            'data_nascimento' => '1990-01-01',
        ]);

        $this->profileService->shouldReceive('getProfile')
            ->once()
            ->with($user)
            ->andReturn($contratado);

        $request = Request::create('/api/contratado', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->contratadoController->show($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($contratado->nome, $data['nome']);
        $this->assertEquals($contratado->email, $data['email']);
        $this->assertEquals($contratado->cpf, $data['cpf']);
        $this->assertEquals($contratado->data_nascimento, $data['data_nascimento']);
    }

    public function test_store_sucesso()
    {
        $data = [
            'nome' => 'Novo Contratado',
            'email' => 'novo@test.com',
            'tipo' => 'contratado',
            'password' => '123456',
            'password_confirmation' => '123456',
            'telefone' => '11999999999',
            'cpf' => '12345678901',
            'data_nascimento' => '1990-01-01',
            'formacoes' => 'Graduação em Análise e Desenvolvimento de Sistemas',
            'habilidades' => 'PHP, Laravel, MySQL',
            'experiencias' => 'Desenvolvedor PHP - 2 anos',
        ];

        $mockResponse = [
            'contratado' => [
                'nome' => 'Novo Contratado',
                'email' => 'novo@test.com',
                'cpf' => '12345678901',
                'data_nascimento' => '1990-01-01'
            ],
            'token' => 'test-token'
        ];

        $this->profileService->shouldReceive('registerProfile')
            ->once()
            ->with($data)
            ->andReturn($mockResponse);

        $request = Mockery::mock(ContratadoRegisterRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($data);

        $response = $this->contratadoController->store($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('contratado', $responseData);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertEquals($data['email'], $responseData['contratado']['email']);
        $this->assertEquals($data['cpf'], $responseData['contratado']['cpf']);
        $this->assertEquals($data['data_nascimento'], $responseData['contratado']['data_nascimento']);
    }

    public function test_update_sucesso()
    {
        $user = User::create([
            'nome' => 'Teste Contratado',
            'email' => 'contratado@test.com',
            'tipo' => 'contratado',
            'password' => Hash::make('123456'),
        ]);

        Contratado::create([
            'user_id' => $user->id,
            'nome' => 'Teste Contratado',
            'telefone' => '11999999999',
            'email' => 'contratado@test.com',
            'cpf' => '12345678901',
            'data_nascimento' => '1990-01-01',
        ]);

        $updateData = [
            'nome' => 'Nome Atualizado',
            'telefone' => '11988888888',
            'sobre' => 'Nova descrição sobre mim',
        ];

        $this->profileService->shouldReceive('updateProfile')
            ->once()
            ->with($user, $updateData)
            ->andReturn(true);

        $request = Mockery::mock(ContratadoUpdateRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($updateData);
        $request->shouldReceive('user')->andReturn($user);
        $request->shouldReceive('all')->andReturn($updateData);

        $response = $this->contratadoController->update($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Perfil atualizado com sucesso!', $responseData['message']);
    }

    public function test_update_password_sucesso()
    {
        $user = User::create([
            'nome' => 'Teste Contratado',
            'email' => 'contratado@test.com',
            'tipo' => 'contratado',
            'password' => Hash::make('123456'),
        ]);

        $passwordData = [
            'current_password' => '123456',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ];

        $this->profileService->shouldReceive('updatePassword')
            ->once()
            ->with($user, $passwordData)
            ->andReturn(true);

        $request = Mockery::mock(PasswordUpdateRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($passwordData);
        $request->shouldReceive('user')->andReturn($user);
        $request->shouldReceive('all')->andReturn($passwordData);

        $response = $this->contratadoController->updatePassword($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Senha atualizada com sucesso!', $responseData['message']);
    }

    public function test_destroy_sucesso()
    {
        $user = User::create([
            'nome' => 'Teste Contratado',
            'email' => 'contratado@test.com',
            'tipo' => 'contratado',
            'password' => Hash::make('123456'),
        ]);

        Contratado::create([
            'user_id' => $user->id,
            'nome' => 'Teste Contratado',
            'telefone' => '11999999999',
            'email' => 'contratado@test.com',
            'cpf' => '12345678901',
            'data_nascimento' => '1990-01-01',
        ]);

        $deleteData = [
            'current_password' => '123456'
        ];

        $this->profileService->shouldReceive('deleteProfile')
            ->once()
            ->with($user, $deleteData)
            ->andReturn(true);

        $request = Mockery::mock(DeleteAccountRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($deleteData);
        $request->shouldReceive('user')->andReturn($user);
        $request->shouldReceive('all')->andReturn($deleteData);

        $response = $this->contratadoController->destroy($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Perfil excluído com sucesso!', $responseData['message']);
    }

    public function test_show_usuario_nao_encontrado()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Usuário não encontrado');

        $this->profileService->shouldReceive('getProfile')
            ->once()
            ->with(null)
            ->andThrow(new \Symfony\Component\HttpKernel\Exception\HttpException(404, 'Usuário não encontrado'));

        $request = Request::create('/api/contratado', 'GET');
        $request->setUserResolver(function () {
            return null;
        });

        $this->contratadoController->show($request);
    }

    public function test_show_perfil_nao_encontrado()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Perfil de contratado não encontrado');

        $user = User::create([
            'nome' => 'Teste Contratado',
            'email' => 'contratado@test.com',
            'tipo' => 'contratado',
            'password' => Hash::make('123456'),
        ]);

        $this->profileService->shouldReceive('getProfile')
            ->once()
            ->with($user)
            ->andThrow(new \Symfony\Component\HttpKernel\Exception\HttpException(404, 'Perfil de contratado não encontrado'));

        $request = Request::create('/api/contratado', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->contratadoController->show($request);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
