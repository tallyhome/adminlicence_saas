<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LegalPagesController extends Controller
{
    /**
     * Affiche l'interface de gestion des pages légales
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $termsPage = LegalPage::getTerms();
        $privacyPage = LegalPage::getPrivacy();
        
        return view('admin.settings.legal_pages', [
            'termsPage' => $termsPage,
            'privacyPage' => $privacyPage,
        ]);
    }
    
    /**
     * Met à jour la page des conditions générales d'utilisation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTerms(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        $termsPage = LegalPage::getTerms();
        $termsPage->title = $request->title;
        $termsPage->content = $request->content;
        $termsPage->last_updated_by = Auth::guard('admin')->id();
        $termsPage->save();
        
        return redirect()->route('admin.settings.legal-pages')
            ->with('success', 'Les conditions générales d\'utilisation ont été mises à jour avec succès.');
    }
    
    /**
     * Met à jour la page de politique de confidentialité
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        $privacyPage = LegalPage::getPrivacy();
        $privacyPage->title = $request->title;
        $privacyPage->content = $request->content;
        $privacyPage->last_updated_by = Auth::guard('admin')->id();
        $privacyPage->save();
        
        return redirect()->route('admin.settings.legal-pages')
            ->with('success', 'La politique de confidentialité a été mise à jour avec succès.');
    }
    
    /**
     * Affiche la page des conditions générales d'utilisation pour les utilisateurs
     *
     * @return \Illuminate\View\View
     */
    public function showTerms()
    {
        $termsPage = LegalPage::getTerms();
        return view('legal.terms', ['page' => $termsPage]);
    }
    
    /**
     * Affiche la page de politique de confidentialité pour les utilisateurs
     *
     * @return \Illuminate\View\View
     */
    public function showPrivacy()
    {
        $privacyPage = LegalPage::getPrivacy();
        return view('legal.privacy', ['page' => $privacyPage]);
    }
}
