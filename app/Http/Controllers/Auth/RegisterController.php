<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/welcome';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Ne pas utiliser $this->middleware() car cette méthode n'est pas disponible
        // dans ce contexte sans le trait RegistersUsers
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
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

        // Si l'utilisateur est créé par un admin connecté, associer l'utilisateur à cet admin
        $adminId = null;
        if (Auth::guard('admin')->check()) {
            $adminId = Auth::guard('admin')->id();
        }

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin_id' => $adminId,
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        // Déclencher l'événement d'inscription
        event(new Registered($user));

        // Connecter l'utilisateur
        Auth::login($user);

        // Rediriger vers la page d'accueil
        return redirect($this->redirectTo)->with('success', 'Votre compte a été créé avec succès !');
    }
}
