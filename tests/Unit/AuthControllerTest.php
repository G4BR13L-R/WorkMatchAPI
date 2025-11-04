<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\AuthController;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Mockery;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private AuthController $authController;
    private AuthService $authService;
    private array $userData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
        $this->authController = new AuthController($this->authService);

        $this->userData = [
            'nome' => 'Teste User',
            'email' => 'user@test.com',
            'tipo' => 'contratante',
            'password' => '123456'
        ];
    }

    public function test_login_sucesso()
    {
        $user = User::create([
            'nome' => $this->userData['nome'],
            'email' => $this->userData['email'],
            'tipo' => $this->userData['tipo'],
            'password' => Hash::make($this->userData['password']),
        ]);

        $loginData = [
            'email' => $this->userData['email'],
            'password' => $this->userData['password'],
        ];

        $request = Mockery::mock(LoginRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($loginData);

        $response = $this->authController->login($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertArrayHasKey('user', $responseData);
        $this->assertArrayHasKey('token', $responseData);

        $this->assertEquals($user->id, $responseData['user']['id']);
        $this->assertEquals($user->email, $responseData['user']['email']);
        $this->assertEquals($user->nome, $responseData['user']['nome']);
        $this->assertEquals($user->tipo, $responseData['user']['tipo']);

        $this->assertArrayNotHasKey('password', $responseData['user']);
        $this->assertArrayNotHasKey('remember_token', $responseData['user']);

        $this->assertIsString($responseData['token']);
        $this->assertNotEmpty($responseData['token']);
        $this->assertStringContainsString('|', $responseData['token']);
    }

    public function test_login_email_invalido()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Credenciais inválidas');

        $loginData = [
            'email' => 'naoexiste@test.com',
            'password' => $this->userData['password'],
        ];

        $request = Mockery::mock(LoginRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($loginData);

        $this->authController->login($request);
    }

    public function test_login_senha_incorreta()
    {
        User::create([
            'nome' => $this->userData['nome'],
            'email' => $this->userData['email'],
            'tipo' => $this->userData['tipo'],
            'password' => Hash::make($this->userData['password']),
        ]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Credenciais inválidas');

        $loginData = [
            'email' => $this->userData['email'],
            'password' => 'senhaErrada',
        ];

        $request = Mockery::mock(LoginRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($loginData);

        $this->authController->login($request);
    }

    public function test_login_email_case_sensitive()
    {
        User::create([
            'nome' => $this->userData['nome'],
            'email' => $this->userData['email'],
            'tipo' => $this->userData['tipo'],
            'password' => Hash::make($this->userData['password']),
        ]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Credenciais inválidas');

        $loginData = [
            'email' => strtoupper($this->userData['email']),
            'password' => $this->userData['password'],
        ];

        $request = Mockery::mock(LoginRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($loginData);

        $this->authController->login($request);
    }

    public function test_logout_sucesso()
    {
        $token = Mockery::mock();
        $token->shouldReceive('delete')->once()->andReturn(true);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('currentAccessToken')->once()->andReturn($token);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')->once()->andReturn($user);

        $response = $this->authController->logout($request);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Logout realizado com sucesso.', $responseData['message']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
