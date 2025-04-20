<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class PaymentSettingsController extends Controller
{
    /**
     * Vérifie si l'utilisateur est un superadmin
     */
    private function checkSuperAdmin()
    {
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->is_super_admin) {
            abort(403, 'Accès non autorisé. Seuls les superadmins peuvent accéder à cette page.');
        }
    }
    
    /**
     * Affiche la page de configuration des paiements
     */
    public function index()
    {
        $this->checkSuperAdmin();
        return view('admin.settings.payment_integration');
    }
    
    /**
     * Met à jour la configuration Stripe
     */
    public function updateStripe(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'stripe_key' => 'required|string',
            'stripe_secret' => 'required|string',
            'stripe_webhook_secret' => 'nullable|string',
        ]);
        
        // Mettre à jour le fichier .env
        $this->updateEnvironmentFile([
            'STRIPE_KEY' => $request->stripe_key,
            'STRIPE_SECRET' => $request->stripe_secret,
            'STRIPE_WEBHOOK_SECRET' => $request->stripe_webhook_secret,
        ]);
        
        // Vider le cache de configuration
        Artisan::call('config:clear');
        
        return redirect()->route('admin.settings.payment-integration')
            ->with('success', 'Configuration Stripe mise à jour avec succès.');
    }
    
    /**
     * Met à jour la configuration PayPal
     */
    public function updatePayPal(Request $request)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'paypal_client_id' => 'required|string',
            'paypal_secret' => 'required|string',
            'paypal_webhook_id' => 'nullable|string',
        ]);
        
        // Mettre à jour le fichier .env
        $this->updateEnvironmentFile([
            'PAYPAL_CLIENT_ID' => $request->paypal_client_id,
            'PAYPAL_SECRET' => $request->paypal_secret,
            'PAYPAL_WEBHOOK_ID' => $request->paypal_webhook_id,
            'PAYPAL_SANDBOX' => $request->has('paypal_sandbox') ? 'true' : 'false',
        ]);
        
        // Vider le cache de configuration
        Artisan::call('config:clear');
        
        return redirect()->route('admin.settings.payment-integration')
            ->with('success', 'Configuration PayPal mise à jour avec succès.');
    }
    
    /**
     * Met à jour les méthodes de paiement activées
     */
    public function updatePaymentMethods(Request $request)
    {
        $this->checkSuperAdmin();
        
        // Mettre à jour le fichier .env
        $this->updateEnvironmentFile([
            'STRIPE_ENABLED' => $request->has('stripe_enabled') ? 'true' : 'false',
            'PAYPAL_ENABLED' => $request->has('paypal_enabled') ? 'true' : 'false',
        ]);
        
        // Vider le cache de configuration
        Artisan::call('config:clear');
        
        return redirect()->route('admin.settings.payment-integration')
            ->with('success', 'Préférences des méthodes de paiement mises à jour avec succès.');
    }
    
    /**
     * Met à jour le fichier .env avec les nouvelles valeurs
     */
    private function updateEnvironmentFile(array $values)
    {
        $envFile = app()->environmentFilePath();
        $envContents = file_get_contents($envFile);
        
        foreach ($values as $key => $value) {
            // Échapper les caractères spéciaux dans la valeur
            $value = str_replace('"', '\"', $value);
            
            // Vérifier si la clé existe déjà
            if (preg_match("/^{$key}=.*/m", $envContents)) {
                // Mettre à jour la valeur existante
                $envContents = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $envContents);
            } else {
                // Ajouter une nouvelle clé
                $envContents .= PHP_EOL . "{$key}=\"{$value}\"";
            }
        }
        
        // Écrire les modifications dans le fichier .env
        file_put_contents($envFile, $envContents);
    }
}
