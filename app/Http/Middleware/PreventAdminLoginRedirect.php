<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminLoginRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtenir la réponse
        $response = $next($request);
        
        // Vérifier si la réponse est une redirection
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            // Obtenir l'URL de redirection
            $targetUrl = $response->getTargetUrl();
            
            // Journaliser la redirection pour le débogage
            \Illuminate\Support\Facades\Log::info('Redirection interceptée', [
                'from' => $request->url(),
                'to' => $targetUrl,
                'auth_web' => auth()->guard('web')->check(),
                'auth_admin' => auth()->guard('admin')->check()
            ]);
            
            // Si la redirection est vers /admin/login et que l'utilisateur est déjà connecté avec le garde 'web'
            if (str_contains($targetUrl, '/admin/login') && auth()->guard('web')->check()) {
                // Rediriger vers le tableau de bord utilisateur à la place
                return redirect('/dashboard');
            }
        }
        
        return $response;
    }
}
