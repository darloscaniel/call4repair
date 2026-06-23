<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mantém o contrato de JSON em arrays "crus" (sem o wrapper "data"),
        // preservando a forma esperada pelo front-end.
        JsonResource::withoutWrapping();

        // Limita a abertura pública de chamados: 5 requisições por minuto por IP.
        RateLimiter::for('calls', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
