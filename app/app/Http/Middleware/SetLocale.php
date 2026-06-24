<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Resolve the application locale from the Accept-Language header,
     * falling back to the configured default when unsupported.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['pt_BR', 'en'];

        $requested = $request->getPreferredLanguage(['pt-BR', 'en']);
        $normalized = str_replace('-', '_', (string) $requested);

        // getPreferredLanguage returns "pt_BR"/"en"; guard against unsupported values.
        if (in_array($normalized, $supported, true)) {
            app()->setLocale($normalized);
        }

        return $next($request);
    }
}
