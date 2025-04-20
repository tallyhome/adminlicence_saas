<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        // Ne pas rediriger si l'URL contient 'subscription' ou 'checkout'
        $path = $request->path();
        if (str_contains($path, 'subscription') || str_contains($path, 'checkout')) {
            return $next($request);
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Si l'utilisateur est authentifié en tant qu'admin, redirigez-le vers le tableau de bord admin
                if ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                // Pour tout autre type d'authentification, redirigez vers la page d'accueil
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
