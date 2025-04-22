<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        // Ne pas utiliser $this->middleware() directement
        // car la méthode n'est pas disponible dans ce contexte
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Journaliser les informations pour le débogage
        \Illuminate\Support\Facades\Log::info('Tentative de connexion', [
            'email' => $request->email,
            'remember' => $request->has('remember'),
        ]);

        // Vérifier manuellement les identifiants
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user) {
            \Illuminate\Support\Facades\Log::info('Utilisateur trouvé', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            // Vérifier le mot de passe
            if (\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                \Illuminate\Support\Facades\Log::info('Mot de passe valide, connexion manuelle');
                
                // Connecter l'utilisateur manuellement
                \Illuminate\Support\Facades\Auth::login($user, $request->has('remember'));
                
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }
                
                return $this->sendLoginResponse($request);
            } else {
                \Illuminate\Support\Facades\Log::warning('Mot de passe invalide', [
                    'email' => $request->email,
                ]);
            }
        } else {
            \Illuminate\Support\Facades\Log::warning('Utilisateur non trouvé', [
                'email' => $request->email,
            ]);
        }

        // Si on arrive ici, c'est que la connexion a échoué
        // Continuer avec le processus standard de Laravel
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
