<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TranslationService;

class ApiDocumentationController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function index()
    {
        $availableLanguages = $this->translationService->getAvailableLocales();
        $currentLanguage = $this->translationService->getLocale();

        return view('admin.api-documentation', [
            'availableLanguages' => $availableLanguages,
            'currentLanguage' => $currentLanguage
        ]);
    }

    public function licenceDocumentation()
    {
        $availableLanguages = $this->translationService->getAvailableLocales();
        $currentLanguage = $this->translationService->getLocale();

        return view('admin.licence-documentation', [
            'availableLanguages' => $availableLanguages,
            'currentLanguage' => $currentLanguage
        ]);
    }
}