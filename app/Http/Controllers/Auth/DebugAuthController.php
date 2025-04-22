<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DebugAuthController extends Controller
{
    /**
     * Affiche une page de débogage pour l'authentification
     *
     * @return \Illuminate\View\View
     */
    public function showDebugPage()
    {
        return view('auth.debug');
    }

    /**
     * Vérifie les identifiants fournis
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkCredentials(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rechercher l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        // Journaliser les informations pour le débogage
        Log::info('Tentative de vérification des identifiants', [
            'email' => $request->email,
            'user_exists' => $user ? true : false,
            'password_length' => strlen($request->password),
        ]);

        if (!$user) {
            return back()->with('error', 'Aucun utilisateur trouvé avec cette adresse e-mail.');
        }

        // Vérifier si le mot de passe correspond
        $passwordMatches = Hash::check($request->password, $user->password);
        
        Log::info('Vérification du mot de passe', [
            'password_matches' => $passwordMatches,
        ]);

        if (!$passwordMatches) {
            return back()->with('error', 'Le mot de passe ne correspond pas.');
        }

        return back()->with('success', 'Les identifiants sont valides. Vous pouvez vous connecter.');
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'new_password' => 'required|min:8',
        ]);

        // Rechercher l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Aucun utilisateur trouvé avec cette adresse e-mail.');
        }

        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->new_password);
        $user->save();

        Log::info('Mot de passe réinitialisé pour l\'utilisateur', [
            'email' => $user->email,
        ]);

        return back()->with('success', 'Le mot de passe a été réinitialisé avec succès.');
    }

    /**
     * Crée un nouvel utilisateur de test
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createTestUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        Log::info('Utilisateur de test créé', [
            'id' => $user->id,
            'email' => $user->email,
        ]);

        return back()->with('success', 'Utilisateur de test créé avec succès.');
    }
}
