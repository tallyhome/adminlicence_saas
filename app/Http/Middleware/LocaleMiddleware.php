<?php

namespace App\Http\Middleware;

use App\Services\TranslationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
            if (in_array($locale, config('app.available_locales', []))) {
                app()->setLocale($locale);
                session()->put('locale', $locale);
                session()->save();
            }
        }
        // Sinon, utiliser la langue de la session
        else if (session()->has('locale')) {
            $locale = session()->get('locale');
            if (in_array($locale, config('app.available_locales', []))) {
                app()->setLocale($locale);
            }
        }
        // Sinon, essayer de détecter la langue du navigateur
        else {
            $locale = $request->getPreferredLanguage(config('app.available_locales', []));
            if ($locale) {
                app()->setLocale($locale);
                session()->put('locale', $locale);
                session()->save();
            }
        }

        // S'assurer que la langue est appliquée
        $currentLocale = app()->getLocale();
        if ($currentLocale !== session()->get('locale')) {
            app()->setLocale(session()->get('locale', $currentLocale));
        }

        return $next($request);
    }
}