<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TranslationService;

class ClientExampleController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Affiche la page d'exemples d'intégration client
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $availableLanguages = $this->translationService->getAvailableLocales();
        $currentLanguage = $this->translationService->getCurrentLanguage();

        return view('admin.client-example', [
            'availableLanguages' => $availableLanguages,
            'currentLanguage' => $currentLanguage
        ]);
    }
}