<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->isInstalled()) {
            $isCpanel = $this->isCpanel();
            $installPath = $isCpanel ? '/public/install' : '/install';
            
            if (!$request->is($installPath . '*')) {
                return redirect($installPath);
            }
        }

        return $next($request);
    }

    /**
     * Vérifier si l'application est installée
     *
     * @return bool
     */
    private function isInstalled()
    {
        return file_exists(base_path('.env')) && 
               env('APP_INSTALLED', false) === true;
    }

    /**
     * Détecter si l'application est sur cPanel
     *
     * @return bool
     */
    private function isCpanel()
    {
        $cpanelIndicators = [
            '/home',
            '/public_html',
            'cpanel',
            '.cpanel',
            'cgi-bin'
        ];

        $serverPath = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        foreach ($cpanelIndicators as $indicator) {
            if (str_contains($serverPath, $indicator) || str_contains($requestUri, $indicator)) {
                return true;
            }
        }

        return false;
    }
} 