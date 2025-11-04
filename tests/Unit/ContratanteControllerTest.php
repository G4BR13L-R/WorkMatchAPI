<?php

namespace Tests\Unit\Controllers\Contratante;

use App\Http\Controllers\Contratante\ContratanteController;
use App\Models\User;
use App\Models\Contratante;
use App\Services\Contratante\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Http\Requests\ContratanteRegisterRequest;
use App\Http\Requests\ContratanteUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\DeleteAccountRequest;

class ContratanteControllerTest extends TestCase
{
    use RefreshDatabase;

    private ContratanteController $contratanteController;
    private ProfileService $profileService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->profileService = new ProfileService();
        $this->contratanteController = new ContratanteController($this->profileService);
    }

    public function test_show_sucesso()
    {
        $user = User::create([
            'nome' => 'Teste User',
            'email' => 'contratante@test.com',
            'tipo' => 'contratante',
            'password' => Hash::make('123456'),
        ]);

        $contratante = Contratante::create([
            'user_id' => $user->id,
            'nome' => 'Teste User',
            'telefone' => '11999999999',
            'email' => 'contratante@test.com',
            'cnpj' => '12345678901234',
            'razao_social' => 'Empresa Teste LTDA',
            'nome_fantasia' => 'Empresa Teste',
        ]);

        $request = Request::create('/api/contratante', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->contratanteController->show($request);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($contratante->nome, $data['nome']);
        $this->assertEquals($contratante->email, $data['email']);
    }

    public function test_store_sucesso()
    {
        $data = [
            'nome' => 'Novo Contratante',
            'email' => 'novo@test.com',
            'password' => '123456',
            'telefone' => '11999999999',
            'cnpj' => '12345678901234',
            'razao_social' => 'Nova Empresa LTDA',
            'nome_fantasia' => 'Nova Empresa',
        ];

        $request = Mockery::mock(ContratanteRegisterRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($data);

        $response = $this->contratanteController->store($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('contratante', $responseData);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertEquals($data['email'], $responseData['contratante']['email']);
    }

    public function test_update_sucesso()
    {
        $user = User::create([
            'nome' => 'Teste User',
            'email' => 'contratante@test.com',
            'tipo' => 'contratante',
            'password' => Hash::make('123456'),
        ]);

        Contratante::create([
            'user_id' => $user->id,
            'nome' => 'Teste User',
            'telefone' => '11999999999',
            'email' => 'contratante@test.com',
            'cnpj' => '12345678901234',
            'razao_social' => 'Empresa Teste LTDA',
            'nome_fantasia' => 'Empresa Teste',
        ]);

        $updateData = [
            'nome' => 'Nome Atualizado',
            'telefone' => '11988888888',
            'nome_fantasia' => 'Empresa Atualizada',
        ];

        $request = Mockery::mock(ContratanteUpdateRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($updateData);
        $request->shouldReceive('user')->andReturn($user);

        $response = $this->contratanteController->update($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Perfil atualizado com sucesso!', $responseData['message']);
    }

    public function test_update_password_sucesso()
    {
        $user = User::create([
            'nome' => 'Teste User',
            'email' => 'contratante@test.com',
            'tipo' => 'contratante',
            'password' => Hash::make('123456'),
        ]);

        $passwordData = [
            'current_password' => '123456',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ];

        $request = Mockery::mock(PasswordUpdateRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($passwordData);
        $request->shouldReceive('user')->andReturn($user);

        $response = $this->contratanteController->updatePassword($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Senha atualizada com sucesso!', $responseData['message']);
    }

    public function test_destroy_sucesso()
    {
        $user = User::create([
            'nome' => 'Teste User',
            'email' => 'contratante@test.com',
            'tipo' => 'contratante',
            'password' => Hash::make('123456'),
        ]);

        Contratante::create([
            'user_id' => $user->id,
            'nome' => 'Teste User',
            'telefone' => '11999999999',
            'email' => 'contratante@test.com',
            'cnpj' => '12345678901234',
            'razao_social' => 'Empresa Teste LTDA',
            'nome_fantasia' => 'Empresa Teste',
        ]);

        $deleteData = [
            'current_password' => '123456'
        ];

        $request = Mockery::mock(DeleteAccountRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($deleteData);
        $request->shouldReceive('user')->andReturn($user);

        $response = $this->contratanteController->destroy($request);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Perfil deletado com sucesso!', $responseData['message']);
    }

    public function test_show_usuario_nao_encontrado()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Usuário não encontrado');

        $request = Request::create('/api/contratante', 'GET');
        $request->setUserResolver(function () {
            return null;
        });

        $this->contratanteController->show($request);
    }

    public function test_show_perfil_nao_encontrado()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Perfil de contratante não encontrado');

        $user = User::create([
            'nome' => 'Teste User',
            'email' => 'contratante@test.com',
            'tipo' => 'contratante',
            'password' => Hash::make('123456'),
        ]);

        $request = Request::create('/api/contratante', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->contratanteController->show($request);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
