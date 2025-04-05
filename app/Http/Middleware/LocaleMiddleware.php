<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Liste des langues disponibles dans l'application
     *
     * @var array
     */
    protected $availableLocales = [
        'en', // Anglais
        'fr', // Français
        'es', // Espagnol
        'de', // Allemand
        'it', // Italien
        'pt', // Portugais
        'nl', // Néerlandais
        'ru', // Russe
        'zh', // Chinois
        'ja'  // Japonais
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si une langue est spécifiée dans l'URL
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            
            // Vérifier si la langue demandée est disponible
            if (in_array($locale, $this->availableLocales)) {
                // Stocker la langue dans la session
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        } 
        // Sinon, utiliser la langue stockée en session si elle existe
        elseif (Session::has('locale') && in_array(Session::get('locale'), $this->availableLocales)) {
            App::setLocale(Session::get('locale'));
        } 
        // Sinon, détecter la langue du navigateur
        else {
            $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE') ?? '', 0, 2);
            
            if (in_array($browserLocale, $this->availableLocales)) {
                Session::put('locale', $browserLocale);
                App::setLocale($browserLocale);
            }
        }
        
        return $next($request);
    }
}