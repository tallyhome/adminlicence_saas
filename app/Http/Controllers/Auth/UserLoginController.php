<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserLoginController extends Controller
{
    /**
     * Affiche le formulaire de connexion pour les utilisateurs.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.user-login');
    }

    /**
     * Traite la demande de connexion pour les utilisateurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Journaliser les informations pour le débogage
        Log::info('Tentative de connexion utilisateur', [
            'email' => $request->email,
            'remember' => $request->has('remember'),
        ]);

        // Rechercher l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('Utilisateur non trouvé', [
                'email' => $request->email,
            ]);
            
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Ces identifiants ne correspondent à aucun utilisateur.']);
        }

        // Vérifier si le mot de passe correspond
        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Mot de passe incorrect', [
                'email' => $request->email,
            ]);
            
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Ces identifiants ne correspondent à aucun utilisateur.']);
        }

        // Connecter l'utilisateur avec le garde 'web' explicitement
        Auth::guard('web')->login($user, $request->has('remember'));
        
        // Vérifier que l'utilisateur est bien connecté
        if (Auth::guard('web')->check()) {
            Log::info('Utilisateur connecté avec succès', [
                'user_id' => $user->id,
                'email' => $user->email,
                'guard' => 'web'
            ]);
            
            // Forcer la redirection vers le tableau de bord utilisateur sans utiliser intended()
            // pour éviter les redirections incorrectes
            return redirect('/dashboard');
        } else {
            Log::error('Échec de connexion après Auth::login', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Problème de connexion. Veuillez réessayer.']);
        }
    }

    /**
     * Déconnecte l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
