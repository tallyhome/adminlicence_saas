<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    /**
     * Affiche la page de bienvenue avec les plans d'abonnement pour les nouveaux utilisateurs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Récupérer les plans actifs
        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        
        return view('subscription.welcome', compact('plans'));
    }
}
