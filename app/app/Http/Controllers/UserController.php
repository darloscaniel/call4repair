<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => __('messages.auth.invalid_credentials')], 401);
        }

        return response()->json([
            'token'   => $token,
            'message' => __('messages.auth.login_success'),
            'user'    => JWTAuth::user(),
        ]);
    }
}
