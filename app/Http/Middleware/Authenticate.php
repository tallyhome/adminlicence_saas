<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }
        
        // Ne pas rediriger si l'URL contient 'payment', 'subscription' ou 'checkout'
        $path = $request->path();
        if (str_contains($path, 'payment') || str_contains($path, 'subscription') || str_contains($path, 'checkout')) {
            return null;
        }
        
        // SOLUTION RADICALE : toujours rediriger vers la page de connexion utilisateur
        return '/user/login';
    }
}