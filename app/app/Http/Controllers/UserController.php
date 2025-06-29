<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required']
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }

        // Aqui você pode gerar token, JWT ou sessão

        return response()->json([
            'token' => $token,
            'message' => 'Login realizado com sucesso!',
            'user' => $user,
        ]);
    }
}
