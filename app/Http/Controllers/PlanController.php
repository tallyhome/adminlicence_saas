<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\StripeService;
use App\Services\PayPalService;

class PlanController extends Controller
{
    protected $stripeService;
    protected $paypalService;

    public function __construct(StripeService $stripeService, PayPalService $paypalService)
    {
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
    }

    /**
     * Afficher la liste des plans disponibles
     */
    public function index(): JsonResponse
    {
        $plans = Plan::active()->get();
        return response()->json(['plans' => $plans]);
    }

    /**
     * Afficher les détails d'un plan spécifique
     */
    public function show(Plan $plan): JsonResponse
    {
        return response()->json(['plan' => $plan]);
    }

    /**
     * Souscrire à un plan
     */
    public function subscribe(Request $request, Plan $plan): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,paypal',
            'billing_cycle' => 'required|in:monthly,yearly'
        ]);

        $tenant = $request->user()->tenant;
        
        // Vérifier si l'utilisateur a déjà un abonnement actif
        $activeSubscription = $tenant->subscriptions()
            ->where('status', 'active')
            ->first();

        if ($activeSubscription) {
            return response()->json([
                'message' => 'Vous avez déjà un abonnement actif'
            ], 400);
        }

        try {
            // Créer l'abonnement selon le mode de paiement choisi
            if ($request->payment_method === 'stripe') {
                $subscription = $this->stripeService->createSubscription($tenant, $plan, $request->all());
            } else {
                $subscription = $this->paypalService->createSubscription($tenant, $plan, $request->all());
            }

            return response()->json([
                'message' => 'Abonnement créé avec succès',
                'subscription' => $subscription
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de l\'abonnement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Annuler un abonnement
     */
    public function cancel(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $subscription = $tenant->subscriptions()
            ->where('status', 'active')
            ->firstOrFail();

        try {
            if ($subscription->payment_method === 'stripe') {
                $this->stripeService->cancelSubscription($subscription);
            } else {
                $this->paypalService->cancelSubscription($subscription);
            }

            $subscription->cancel();

            return response()->json([
                'message' => 'Abonnement annulé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'annulation de l\'abonnement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reprendre un abonnement annulé
     */
    public function resume(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $subscription = $tenant->subscriptions()
            ->where('status', 'active')
            ->where('canceled_at', '!=', null)
            ->firstOrFail();

        try {
            if ($subscription->payment_method === 'stripe') {
                $this->stripeService->resumeSubscription($subscription);
            } else {
                $this->paypalService->resumeSubscription($subscription);
            }

            $subscription->resume();

            return response()->json([
                'message' => 'Abonnement repris avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la reprise de l\'abonnement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}