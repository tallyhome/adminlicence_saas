<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class DirectRegisterController extends Controller
{
    /**
     * Affiche le formulaire d'inscription personnalisé.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        // Vider la session pour s'assurer qu'il n'y a pas de problème d'authentification
        session()->flush();
        
        // Régénérer un nouveau token CSRF
        session()->regenerateToken();
        
        return view('auth.register');
    }

    /**
     * Traite l'inscription d'un utilisateur de manière directe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        Log::info('Tentative d\'inscription', [
            'request_data' => $request->except(['password', 'password_confirmation']),
            'has_csrf_token' => $request->has('_token'),
            'csrf_token' => $request->input('_token'),
            'session_token' => csrf_token(),
            'token_match' => $request->has('_token') && $request->input('_token') === csrf_token()
        ]);
        
        // Valider les données
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ], [
            'terms.required' => 'Vous devez accepter les conditions d\'utilisation et la politique de confidentialité.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation et la politique de confidentialité.'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée lors de l\'inscription', [
                'errors' => $validator->errors()->toArray()
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Créer l'utilisateur avec le modèle User de Laravel
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'terms_accepted' => true,
                'terms_accepted_at' => now(),
            ]);

            Log::info('Utilisateur créé avec succès', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Connecter l'utilisateur avec Auth de Laravel
            Auth::login($user);
            
            // Régénérer la session pour éviter les problèmes de session fixation
            session()->regenerate();

            // Rediriger vers la page d'accueil
            return redirect('/welcome')->with('success', 'Votre compte a été créé avec succès !');
        } catch (\Exception $e) {
            // Journaliser l'erreur de manière détaillée
            Log::error('Erreur lors de l\'inscription directe', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation']),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // Rediriger avec un message d'erreur
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['general' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.']);
        }
    }
}
