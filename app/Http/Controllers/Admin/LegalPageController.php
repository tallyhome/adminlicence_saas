<?php

namespace App\Http\Controllers\Admin;

use App\Models\LegalPage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LegalPageController extends Controller
{
    /**
     * Affiche la liste des pages légales.
     */
    public function index()
    {
        // Vérifier si l'utilisateur est un super-admin
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')->with('error', 'Accès non autorisé.');
        }
        
        $terms = LegalPage::getTerms();
        $privacy = LegalPage::getPrivacy();
        
        return view('admin.legal.index', compact('terms', 'privacy'));
    }
    
    /**
     * Affiche le formulaire d'édition des conditions d'utilisation.
     */
    public function editTerms()
    {
        // Vérifier si l'utilisateur est un super-admin
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')->with('error', 'Accès non autorisé.');
        }
        
        $page = LegalPage::getTerms();
        $type = 'terms';
        
        return view('admin.legal.edit', compact('page', 'type'));
    }
    
    /**
     * Affiche le formulaire d'édition de la politique de confidentialité.
     */
    public function editPrivacy()
    {
        // Vérifier si l'utilisateur est un super-admin
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')->with('error', 'Accès non autorisé.');
        }
        
        $page = LegalPage::getPrivacy();
        $type = 'privacy';
        
        return view('admin.legal.edit', compact('page', 'type'));
    }
    
    /**
     * Met à jour une page légale.
     */
    public function update(Request $request, $type)
    {
        // Vérifier si l'utilisateur est un super-admin
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')->with('error', 'Accès non autorisé.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        if ($type === 'terms') {
            $page = LegalPage::getTerms();
        } elseif ($type === 'privacy') {
            $page = LegalPage::getPrivacy();
        } else {
            return redirect()->route('admin.legal.index')->with('error', 'Type de page non valide.');
        }
        
        $page->title = $request->title;
        $page->content = $request->content;
        $page->last_updated_by = Auth::guard('admin')->id();
        $page->save();
        
        return redirect()->route('admin.legal.index')->with('success', 'Page mise à jour avec succès.');
    }
}
