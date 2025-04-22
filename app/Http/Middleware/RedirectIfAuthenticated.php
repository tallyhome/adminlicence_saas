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
        $guards = empty($guards) ? ['web'] : $guards;

        // Journaliser pour le débogage
        \Illuminate\Support\Facades\Log::info('RedirectIfAuthenticated middleware', [
            'path' => $request->path(),
            'guards' => $guards,
            'admin_check' => Auth::guard('admin')->check(),
            'web_check' => Auth::guard('web')->check()
        ]);

        // Ne pas rediriger si l'URL contient 'subscription' ou 'checkout'
        $path = $request->path();
        if (str_contains($path, 'subscription') || str_contains($path, 'checkout')) {
            return $next($request);
        }

        // Vérifier si l'utilisateur tente d'accéder à la page de connexion admin
        if ($request->path() === 'admin/login') {
            // Si l'utilisateur est déjà connecté en tant qu'utilisateur normal, le rediriger vers son tableau de bord
            if (Auth::guard('web')->check() && !Auth::guard('admin')->check()) {
                return redirect('/dashboard');
            }
        }

        // Vérifier si l'utilisateur tente d'accéder à la page de connexion utilisateur
        if ($request->routeIs('user.login') || $request->path() === 'user/login') {
            // Si l'utilisateur est déjà connecté, le rediriger vers son tableau de bord
            if (Auth::guard('web')->check()) {
                return redirect('/dashboard');
            }
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Si l'utilisateur est authentifié en tant qu'admin, redirigez-le vers le tableau de bord admin
                if ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                
                // Vérifier si l'URL demandée est une URL admin
                if (str_starts_with($request->path(), 'admin/')) {
                    // Si un utilisateur normal tente d'accéder à une page admin, le rediriger vers son tableau de bord
                    return redirect('/dashboard');
                }
                
                // Pour tout autre type d'authentification, redirigez vers le tableau de bord utilisateur
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
