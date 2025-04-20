<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\PayPalService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $stripeService;
    protected $paypalService;

    public function __construct(StripeService $stripeService, PayPalService $paypalService)
    {
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
    }

    public function setupIntent()
    {
        // Seuls les admins/superadmins peuvent créer un setup intent
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        try {
            $intent = $this->stripeService->createSetupIntent();
            return response()->json(['clientSecret' => $intent->client_secret]);
        } catch (\Exception $e) {
            Log::error('Failed to create setup intent: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la configuration du paiement.'], 500);
        }
    }

    public function storePaymentMethod(Request $request)
    {
        // Seuls les admins/superadmins peuvent ajouter un moyen de paiement
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        $request->validate([
            'payment_method_id' => 'required|string',
            'is_default' => 'boolean'
        ]);

        try {
            $paymentMethod = $this->stripeService->attachPaymentMethod(
                $request->user()->tenant,
                $request->payment_method_id,
                $request->is_default ?? false
            );

            return response()->json($paymentMethod);
        } catch (\Exception $e) {
            Log::error('Failed to store payment method: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de l\'enregistrement du moyen de paiement.'], 500);
        }
    }

    public function deletePaymentMethod(Request $request, PaymentMethod $paymentMethod)
    {
        // Seuls les admins/superadmins peuvent supprimer un moyen de paiement
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        try {
            if ($request->user()->tenant_id !== $paymentMethod->tenant_id) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            if ($paymentMethod->provider === 'stripe') {
                $this->stripeService->detachPaymentMethod($paymentMethod->provider_id);
            }

            $paymentMethod->delete();

            return response()->json(['message' => 'Moyen de paiement supprimé avec succès']);
        } catch (\Exception $e) {
            Log::error('Failed to delete payment method: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la suppression du moyen de paiement.'], 500);
        }
    }

    public function createPayPalOrder(Request $request)
    {
        // Seuls les admins/superadmins peuvent créer une commande PayPal
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        try {
            $subscription = Subscription::findOrFail($request->subscription_id);
            $order = $this->paypalService->createOrder($subscription);
            return response()->json(['id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Failed to create PayPal order: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la création de la commande PayPal.'], 500);
        }
    }

    public function capturePayPalOrder(Request $request)
    {
        // Seuls les admins/superadmins peuvent capturer une commande PayPal
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        try {
            $order = $this->paypalService->captureOrder($request->order_id);
            return response()->json($order);
        } catch (\Exception $e) {
            Log::error('Failed to capture PayPal order: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la capture du paiement PayPal.'], 500);
        }
    }

    /**
     * Affiche le formulaire de paiement Stripe pour un plan spécifique
     * 
     * @param int $planId
     * @return \Illuminate\View\View
     */
    public function showStripeForm($planId)
    {
        // Récupérer le plan depuis la base de données
        $plan = Plan::findOrFail($planId);
        
        // Vérifier si le plan est actif
        if (!$plan->is_active) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Ce plan n\'est pas disponible actuellement.');
        }
        
        // Récupérer la clé publique Stripe
        $stripeKey = config('services.stripe.key');
        
        // Journaliser l'accès à la page
        Log::info('Accès au formulaire de paiement Stripe pour le plan: ' . $plan->name);
        
        return view('payment.stripe', compact('plan', 'stripeKey'));
    }
    
    /**
     * Traite un paiement Stripe
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processStripe(Request $request)
    {
        // Journaliser les données reçues pour le débogage
        Log::info('Stripe payment request received', $request->all());
        
        $request->validate([
            'plan_id' => 'required|string',
            'payment_method_id' => 'nullable|string',
        ]);
        
        try {
            // Récupérer le plan
            $plan = Plan::findOrFail($request->plan_id);
            
            // Simuler un succès pour le débogage
            Log::info('Stripe payment simulated success for plan: ' . $plan->name);
            
            return redirect()->route('payment.success')
                ->with('success', 'Paiement Stripe traité avec succès (simulation).');
        } catch (\Exception $e) {
            Log::error('Erreur de paiement Stripe : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche le formulaire de paiement PayPal pour un plan spécifique
     * 
     * @param int $planId
     * @return \Illuminate\View\View
     */
    public function showPaypalForm($planId)
    {
        // Récupérer le plan depuis la base de données
        $plan = Plan::findOrFail($planId);
        
        // Vérifier si le plan est actif
        if (!$plan->is_active) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Ce plan n\'est pas disponible actuellement.');
        }
        
        // Récupérer la clé publique PayPal
        $paypalClientId = config('services.paypal.client_id');
        
        // Journaliser l'accès à la page
        Log::info('Accès au formulaire de paiement PayPal pour le plan: ' . $plan->name);
        
        return view('payment.paypal', compact('plan', 'paypalClientId'));
    }
    
    /**
     * Traite un paiement PayPal
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPaypal(Request $request)
    {
        // Journaliser les données reçues pour le débogage
        Log::info('PayPal payment request received', $request->all());
        
        $request->validate([
            'plan_id' => 'required|string',
        ]);
        
        try {
            // Récupérer le plan
            $plan = Plan::findOrFail($request->plan_id);
            
            // Simuler un succès pour le débogage
            Log::info('PayPal payment simulated success for plan: ' . $plan->name);
            
            return redirect()->route('payment.success')
                ->with('success', 'Paiement PayPal traité avec succès (simulation).');
        } catch (\Exception $e) {
            Log::error('Erreur de paiement PayPal : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche la page de succès après un paiement réussi
     * 
     * @return \Illuminate\View\View
     */
    public function success()
    {
        return view('payment.success');
    }
    
    /**
     * Vérifie si l'utilisateur connecté est un admin ou un super-admin
     *
     * @return bool
     */
    protected function isAdminOrSuperAdmin()
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
            return false;
        }
        
        return true;
    }
}