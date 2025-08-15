<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            abort(401, 'Credenciais inválidas');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user->toArray(),
            'token' => $token,
        ];
    }
}
