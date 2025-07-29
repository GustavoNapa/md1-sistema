<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Pennant\Feature;

class CheckFeatureFlag
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        if (!Feature::active($featureKey)) {
            // Feature flag desativada, redireciona para o dashboard com mensagem de erro
            return redirect()->route("home")->with("error", "Funcionalidade \"" . $featureKey . "\" desativada.");
        }

        return $next($request);
    }
}


