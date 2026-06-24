<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken
{
    /**
     * Double-submit CSRF protection for cookie-authenticated, state-changing
     * requests. Only enforced when the request relies on the auth cookie
     * (no Authorization header) — Bearer clients are not exposed to CSRF.
     *
     * Must run before AuthenticateFromCookie so the original (non-injected)
     * Authorization header presence is observed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isStateChanging = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
        $usesAuthCookie = $request->cookie('token') && !$request->headers->has('Authorization');

        if ($isStateChanging && $usesAuthCookie) {
            $header = (string) $request->header('X-XSRF-TOKEN');
            $cookie = (string) $request->cookie('XSRF-TOKEN');

            if ($header === '' || $cookie === '' || !hash_equals($cookie, $header)) {
                return response()->json(['message' => 'CSRF token mismatch.'], 419);
            }
        }

        return $next($request);
    }
}
