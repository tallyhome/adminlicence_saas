<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
// Ne pas utiliser l'événement Registered qui cause l'erreur
// use Illuminate\Auth\Events\Registered;

class CustomRegisterController extends Controller
{
    /**
     * Affiche le formulaire d'inscription.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Traite la demande d'inscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Si l'utilisateur est créé par un admin connecté, associer l'utilisateur à cet admin
            $adminId = null;
            if (Auth::guard('admin')->check()) {
                $adminId = Auth::guard('admin')->id();
            }

            // Créer l'utilisateur
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->admin_id = $adminId;
            $user->terms_accepted = true;
            $user->terms_accepted_at = now();
            $user->save();

            // Connecter l'utilisateur manuellement
            Auth::login($user);

            // NE PAS déclencher l'événement d'inscription qui cause l'erreur
            // event(new Registered($user));

            // Envoyer un email de vérification manuellement si nécessaire
            // $user->sendEmailVerificationNotification();

            // Rediriger vers la page d'accueil
            return redirect('/welcome')->with('success', 'Votre compte a été créé avec succès !');
        } catch (\Exception $e) {
            // Journaliser l'erreur
            \Illuminate\Support\Facades\Log::error('Erreur lors de l\'inscription : ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            // Rediriger avec un message d'erreur
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['general' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.']);
        }
    }
}
