<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DirectPaymentController extends Controller
{
    /**
     * Affiche le formulaire de paiement Stripe pour un plan spécifique
     * 
     * @param int $planId
     * @return \Illuminate\View\View
     */
    public function showStripeForm($planId)
    {
        try {
            // Vérifier si le plan existe
            $plan = Plan::find($planId);
            
            // Si le plan n'existe pas, créer un plan fictif pour le débogage
            if (!$plan) {
                $plan = new Plan();
                $plan->id = $planId;
                $plan->name = 'Plan de test';
                $plan->description = 'Ce plan est utilisé pour tester le système de paiement.';
                $plan->price = 9.99;
                $plan->billing_cycle = 'monthly';
                $plan->is_active = true;
                $plan->features = json_encode(['Fonctionnalité 1', 'Fonctionnalité 2', 'Fonctionnalité 3']);
                $plan->trial_days = 0;
                
                Log::info('Plan fictif créé pour le débogage avec ID: ' . $planId);
            }
            
            // Récupérer la clé publique Stripe
            $stripeKey = config('services.stripe.key');
            if (empty($stripeKey)) {
                $stripeKey = 'pk_test_sample'; // Clé de test pour le débogage
            }
            
            // Journaliser l'accès à la page
            Log::info('Accès au formulaire de paiement Stripe pour le plan: ' . $plan->name);
            
            return view('payment.stripe', compact('plan', 'stripeKey'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du formulaire Stripe: ' . $e->getMessage());
            
            // Afficher un message d'erreur sans redirection
            return response()->view('errors.generic', [
                'message' => 'Une erreur est survenue lors du chargement du formulaire de paiement: ' . $e->getMessage()
            ], 500);
        }
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
            
            return response()->view('errors.generic', [
                'message' => 'Une erreur est survenue lors du traitement du paiement: ' . $e->getMessage()
            ], 500);
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
        try {
            // Vérifier si le plan existe
            $plan = Plan::find($planId);
            
            // Si le plan n'existe pas, créer un plan fictif pour le débogage
            if (!$plan) {
                $plan = new Plan();
                $plan->id = $planId;
                $plan->name = 'Plan de test';
                $plan->description = 'Ce plan est utilisé pour tester le système de paiement.';
                $plan->price = 9.99;
                $plan->billing_cycle = 'monthly';
                $plan->is_active = true;
                $plan->features = json_encode(['Fonctionnalité 1', 'Fonctionnalité 2', 'Fonctionnalité 3']);
                $plan->trial_days = 0;
                
                Log::info('Plan fictif créé pour le débogage avec ID: ' . $planId);
            }
            
            // Récupérer la clé publique PayPal
            $paypalClientId = config('services.paypal.client_id');
            if (empty($paypalClientId)) {
                $paypalClientId = 'test_client_id'; // ID client de test pour le débogage
            }
            
            // Journaliser l'accès à la page
            Log::info('Accès au formulaire de paiement PayPal pour le plan: ' . $plan->name);
            
            return view('payment.paypal', compact('plan', 'paypalClientId'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du formulaire PayPal: ' . $e->getMessage());
            
            // Afficher un message d'erreur sans redirection
            return response()->view('errors.generic', [
                'message' => 'Une erreur est survenue lors du chargement du formulaire de paiement: ' . $e->getMessage()
            ], 500);
        }
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
            
            return response()->view('errors.generic', [
                'message' => 'Une erreur est survenue lors du traitement du paiement: ' . $e->getMessage()
            ], 500);
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
