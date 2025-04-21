<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentSettingsController extends Controller
{
    /**
     * Vérifie si l'utilisateur est un superadmin
     */
    private function checkSuperAdmin()
    {
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->isSuperAdmin()) {
            abort(403, 'Accès non autorisé. Seuls les superadmins peuvent accéder à cette page.');
        }
    }
    
    /**
     * Affiche la page de configuration des paiements
     */
    public function index()
    {
        $this->checkSuperAdmin();
        
        // Récupérer les paramètres depuis la base de données ou utiliser les valeurs par défaut
        $stripeEnabled = PaymentSetting::getValue('stripe_enabled', config('payment.stripe.enabled') ? 'true' : 'false');
        $paypalEnabled = PaymentSetting::getValue('paypal_enabled', config('payment.paypal.enabled') ? 'true' : 'false');
        
        // Convertir les valeurs en booléens pour la vue
        $stripeEnabled = ($stripeEnabled === 'true');
        $paypalEnabled = ($paypalEnabled === 'true');
        
        return view('admin.settings.payment_integration', [
            'stripeEnabled' => $stripeEnabled,
            'paypalEnabled' => $paypalEnabled
        ]);
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

        // Loguer les valeurs reçues
        Log::info('Mise à jour configuration Stripe', [
            'stripe_enabled' => $request->has('stripe_enabled') ? 'true' : 'false',
            'stripe_key' => $request->stripe_key,
        ]);

        // Sauvegarder les paramètres dans la base de données
        PaymentSetting::setValue('stripe_enabled', $request->has('stripe_enabled') ? 'true' : 'false', 'Activation de Stripe');
        PaymentSetting::setValue('stripe_key', $request->stripe_key, 'Clé publique Stripe');
        PaymentSetting::setValue('stripe_secret', $request->stripe_secret, 'Clé secrète Stripe');
        
        if ($request->filled('stripe_webhook_secret')) {
            PaymentSetting::setValue('stripe_webhook_secret', $request->stripe_webhook_secret, 'Secret du webhook Stripe');
        }

        $values = [
            'STRIPE_KEY' => $request->stripe_key,
            'STRIPE_SECRET' => $request->stripe_secret,
            'STRIPE_ENABLED' => $request->has('stripe_enabled') ? 'true' : 'false',
        ];

        if ($request->filled('stripe_webhook_secret')) {
            $values['STRIPE_WEBHOOK_SECRET'] = $request->stripe_webhook_secret;
        }

        // On modifie directement le fichier de configuration aussi
        config(['payment.stripe.key' => $request->stripe_key]);
        config(['payment.stripe.secret' => $request->stripe_secret]);
        config(['payment.stripe.enabled' => $request->has('stripe_enabled')]);
        if ($request->filled('stripe_webhook_secret')) {
            config(['payment.stripe.webhook_secret' => $request->stripe_webhook_secret]);
        }

        // Essayer de mettre à jour le fichier .env (mais ne pas échouer si cela ne fonctionne pas)
        try {
            $this->updateEnvironmentFile($values);
        } catch (\Exception $e) {
            Log::warning('Impossible de mettre à jour le fichier .env pour Stripe, mais les paramètres ont été sauvegardés en base de données: ' . $e->getMessage());
        }

        // Vider le cache de configuration
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

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

        // Loguer les valeurs reçues
        Log::info('Mise à jour configuration PayPal', [
            'paypal_enabled' => $request->has('paypal_enabled') ? 'true' : 'false',
            'paypal_sandbox' => $request->has('paypal_sandbox') ? 'sandbox' : 'live',
            'paypal_client_id' => $request->paypal_client_id,
        ]);

        // Sauvegarder les paramètres dans la base de données
        PaymentSetting::setValue('paypal_enabled', $request->has('paypal_enabled') ? 'true' : 'false', 'Activation de PayPal');
        PaymentSetting::setValue('paypal_client_id', $request->paypal_client_id, 'Client ID PayPal');
        PaymentSetting::setValue('paypal_secret', $request->paypal_secret, 'Secret PayPal');
        PaymentSetting::setValue('paypal_mode', $request->has('paypal_sandbox') ? 'sandbox' : 'live', 'Mode PayPal');
        
        if ($request->filled('paypal_webhook_id')) {
            PaymentSetting::setValue('paypal_webhook_id', $request->paypal_webhook_id, 'ID du webhook PayPal');
        }

        $values = [
            'PAYPAL_CLIENT_ID' => $request->paypal_client_id,
            'PAYPAL_SECRET' => $request->paypal_secret,
            'PAYPAL_MODE' => $request->has('paypal_sandbox') ? 'sandbox' : 'live',
            'PAYPAL_ENABLED' => $request->has('paypal_enabled') ? 'true' : 'false',
        ];

        if ($request->filled('paypal_webhook_id')) {
            $values['PAYPAL_WEBHOOK_ID'] = $request->paypal_webhook_id;
        }

        // On modifie directement le fichier de configuration aussi
        config(['payment.paypal.client_id' => $request->paypal_client_id]);
        config(['payment.paypal.secret' => $request->paypal_secret]);
        config(['payment.paypal.mode' => $request->has('paypal_sandbox') ? 'sandbox' : 'live']);
        config(['payment.paypal.enabled' => $request->has('paypal_enabled')]);
        if ($request->filled('paypal_webhook_id')) {
            config(['payment.paypal.webhook_id' => $request->paypal_webhook_id]);
        }

        // Essayer de mettre à jour le fichier .env (mais ne pas échouer si cela ne fonctionne pas)
        try {
            $this->updateEnvironmentFile($values);
        } catch (\Exception $e) {
            Log::warning('Impossible de mettre à jour le fichier .env pour PayPal, mais les paramètres ont été sauvegardés en base de données: ' . $e->getMessage());
        }

        // Vider le cache de configuration
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return redirect()->back()->with('success', 'Les paramètres PayPal ont été mis à jour avec succès.');
    }
    
    /**
     * Met à jour les méthodes de paiement activées
     */
    public function updatePaymentMethods(Request $request)
    {
        $this->checkSuperAdmin();
        
        // Loguer les valeurs reçues pour le débogage
        Log::info('Mise à jour des méthodes de paiement', [
            'stripe_enabled' => $request->has('stripe_enabled') ? 'true' : 'false',
            'paypal_enabled' => $request->has('paypal_enabled') ? 'true' : 'false',
        ]);
        
        try {
            // Sauvegarder les paramètres dans la base de données
            PaymentSetting::setValue('stripe_enabled', $request->has('stripe_enabled') ? 'true' : 'false', 'Activation de Stripe');
            PaymentSetting::setValue('paypal_enabled', $request->has('paypal_enabled') ? 'true' : 'false', 'Activation de PayPal');
            
            // Mettre à jour la configuration en mémoire
            config(['payment.stripe.enabled' => $request->has('stripe_enabled')]);
            config(['payment.paypal.enabled' => $request->has('paypal_enabled')]);
            
            // Essayer également de mettre à jour le fichier .env (mais ne pas échouer si cela ne fonctionne pas)
            try {
                $this->updateEnvironmentFile([
                    'STRIPE_ENABLED' => $request->has('stripe_enabled') ? 'true' : 'false',
                    'PAYPAL_ENABLED' => $request->has('paypal_enabled') ? 'true' : 'false',
                ]);
            } catch (\Exception $e) {
                Log::warning('Impossible de mettre à jour le fichier .env, mais les paramètres ont été sauvegardés en base de données: ' . $e->getMessage());
            }
            
            // Vider le cache de configuration
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            
            return redirect()->route('admin.settings.payment-integration')
                ->with('success', 'Préférences des méthodes de paiement mises à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Exception lors de la mise à jour des méthodes de paiement: ' . $e->getMessage());
            return redirect()->route('admin.settings.payment-integration')
                ->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }
    
    /**
     * Met à jour le fichier .env avec les nouvelles valeurs
     */
    private function updateEnvironmentFile(array $values)
    {
        try {
            $envFile = app()->environmentFilePath();
            Log::info('Chemin du fichier .env: ' . $envFile);
            
            if (!file_exists($envFile)) {
                Log::error('Le fichier .env n\'existe pas: ' . $envFile);
                throw new \Exception("Le fichier .env n'existe pas: {$envFile}");
            }
            
            if (!is_writable($envFile)) {
                Log::error('Le fichier .env n\'est pas accessible en écriture: ' . $envFile);
                throw new \Exception("Le fichier .env n'est pas accessible en écriture: {$envFile}");
            }
            
            $envContents = file_get_contents($envFile);
            
            foreach ($values as $key => $value) {
                // Correction de l'échappement des caractères spéciaux
                $value = str_replace('"', '\"', $value);
                
                Log::info("Mise à jour de la clé .env: {$key} = {$value}");
                
                // Vérifier si la clé existe déjà
                if (preg_match("/^{$key}=.*/m", $envContents)) {
                    // Mettre à jour la valeur existante sans ajouter de guillemets supplémentaires
                    $envContents = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContents);
                } else {
                    // Ajouter une nouvelle clé sans guillemets supplémentaires
                    $envContents .= PHP_EOL . "{$key}={$value}";
                }
            }
            
            // Écrire les modifications dans le fichier .env
            $result = file_put_contents($envFile, $envContents);
            
            if ($result === false) {
                Log::error("Impossible d'écrire dans le fichier .env");
                throw new \Exception("Impossible d'écrire dans le fichier .env");
            }
            
            Log::info('Fichier .env mis à jour avec succès');
            
            // Vider les caches de configuration pour appliquer les modifications
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            
            return true;
        } catch (\Exception $e) {
            // Log l'erreur
            Log::error('Erreur lors de la mise à jour du fichier .env: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
