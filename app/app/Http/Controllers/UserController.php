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

        return $this->respondWithToken($token, [
            'message' => __('messages.auth.login_success'),
        ] + $this->userPayload());
    }

    /**
     * Return the authenticated user with roles/permissions. Used by the SPA
     * to (re)hydrate auth state, since the token cookie is httpOnly.
     */
    public function me(): JsonResponse
    {
        return response()->json($this->userPayload());
    }

    public function refresh(): JsonResponse
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return $this->respondWithToken($token, $this->userPayload());
    }

    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()
            ->json(['message' => __('messages.auth.logged_out')])
            ->withoutCookie('token')
            ->withoutCookie('XSRF-TOKEN');
    }

    /**
     * @return array<string, mixed>
     */
    private function userPayload(): array
    {
        $user = JWTAuth::user();

        return [
            'user'        => $user,
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ];
    }

    /**
     * Build a JSON response that carries the JWT in an httpOnly cookie plus a
     * readable XSRF-TOKEN cookie for double-submit CSRF protection.
     *
     * @param array<string, mixed> $payload
     */
    private function respondWithToken(string $token, array $payload): JsonResponse
    {
        $minutes = (int) config('jwt.ttl');
        $secure = app()->environment('production');

        // token: httpOnly (JS cannot read it); XSRF-TOKEN: readable by axios.
        $tokenCookie = cookie('token', $token, $minutes, '/', null, $secure, true, false, 'Lax');
        $xsrfCookie = cookie('XSRF-TOKEN', bin2hex(random_bytes(20)), $minutes, '/', null, $secure, false, false, 'Lax');

        return response()
            ->json($payload)
            ->withCookie($tokenCookie)
            ->withCookie($xsrfCookie);
    }
}
