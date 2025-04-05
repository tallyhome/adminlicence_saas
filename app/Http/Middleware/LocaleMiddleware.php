<?php

namespace App\Http\Middleware;

use App\Services\TranslationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Le service de traduction
     *
     * @var TranslationService
     */
    protected $translationService;

    /**
     * Constructeur
     *
     * @param TranslationService $translationService
     */
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

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
            
            // Vérifier si la langue demandée est disponible et la définir
            $this->translationService->setLocale($locale);
        } 
        // Sinon, utiliser la détection automatique de langue via le service
        else {
            // Si aucune langue n'est définie en session, essayer de détecter celle du navigateur
            if (!session()->has('locale')) {
                $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE') ?? '', 0, 2);
                $this->translationService->setLocale($browserLocale);
            }
        }
        
        return $next($request);
    }
}