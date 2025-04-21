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
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les autorisations nécessaires pour cette action.');
        }

        $request->validate([
            'stripe_key' => 'required',
            'stripe_secret' => 'required',
        ]);

        $values = [
            'STRIPE_KEY' => $request->stripe_key,
            'STRIPE_SECRET' => $request->stripe_secret,
            'STRIPE_ENABLED' => $request->has('stripe_enabled') ? 'true' : 'false',
        ];

        $success = $this->updateEnvironmentFile($values);

        if (!$success) {
            return redirect()->back()->with('error', 'Impossible de sauvegarder les paramètres Stripe.');
        }

        return redirect()->back()->with('success', 'Les paramètres Stripe ont été mis à jour avec succès.');
    }
    
    /**
     * Met à jour la configuration PayPal
     */
    public function updatePayPal(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les autorisations nécessaires pour cette action.');
        }

        $request->validate([
            'paypal_client_id' => 'required',
            'paypal_secret' => 'required',
        ]);

        $values = [
            'PAYPAL_CLIENT_ID' => $request->paypal_client_id,
            'PAYPAL_SECRET' => $request->paypal_secret,
            'PAYPAL_MODE' => $request->sandbox_mode ? 'sandbox' : 'live',
            'PAYPAL_ENABLED' => $request->has('paypal_enabled') ? 'true' : 'false',
        ];

        $success = $this->updateEnvironmentFile($values);

        if (!$success) {
            return redirect()->back()->with('error', 'Impossible de sauvegarder les paramètres PayPal.');
        }

        return redirect()->back()->with('success', 'Les paramètres PayPal ont été mis à jour avec succès.');
    }
    
    /**
     * Met à jour les méthodes de paiement activées
     */
    public function updatePaymentMethods(Request $request)
    {
        $this->checkSuperAdmin();
        
        // Mettre à jour le fichier .env
        $success = $this->updateEnvironmentFile([
            'STRIPE_ENABLED' => $request->has('stripe_enabled') ? 'true' : 'false',
            'PAYPAL_ENABLED' => $request->has('paypal_enabled') ? 'true' : 'false',
        ]);
        
        if (!$success) {
            return redirect()->route('admin.settings.payment-integration')
                ->with('error', 'Une erreur est survenue lors de la mise à jour des préférences. Veuillez vérifier les permissions du fichier .env.');
        }
        
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
        try {
            $envFile = app()->environmentFilePath();
            
            if (!file_exists($envFile) || !is_writable($envFile)) {
                throw new \Exception("Le fichier .env n'existe pas ou n'est pas accessible en écriture: {$envFile}");
            }
            
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
            $result = file_put_contents($envFile, $envContents);
            
            if ($result === false) {
                throw new \Exception("Impossible d'écrire dans le fichier .env");
            }
            
            // Vider les caches de configuration pour appliquer les modifications
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            
            return true;
        } catch (\Exception $e) {
            // Log l'erreur
            \Log::error('Erreur lors de la mise à jour du fichier .env: ' . $e->getMessage());
            return false;
        }
    }
}
