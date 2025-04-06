<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TranslationService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Change la langue de l'interface d'administration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLanguage(Request $request)
    {
        $locale = $request->input('locale');
        
        if (!in_array($locale, config('app.available_locales', []))) {
            return redirect()->back()->with('error', t('language.invalid_locale'));
        }

        app()->setLocale($locale);
        session()->put('locale', $locale);
        session()->save();

        return redirect()->back()->with('success', t('language.changed_successfully'));
    }
}