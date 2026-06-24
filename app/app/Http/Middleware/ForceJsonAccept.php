<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonAccept
{
    /**
     * Force API requests to be treated as JSON so framework responses
     * (auth 401, validation 422, not-found 404, ...) are always JSON,
     * never an HTML page or a redirect to a non-existent "login" route.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
