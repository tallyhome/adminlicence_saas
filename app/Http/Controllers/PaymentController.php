<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\PayPalService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $stripeService;
    protected $paypalService;

    /**
     * Créer une nouvelle instance du contrôleur.
     */
    public function __construct(StripeService $stripeService, PayPalService $paypalService)
    {
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
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
        Log::info('Stripe payment request received', $request->except(['payment_method_id']));
        
        $request->validate([
            'plan_id' => 'required',
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
            'plan_id' => 'required',
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
}
