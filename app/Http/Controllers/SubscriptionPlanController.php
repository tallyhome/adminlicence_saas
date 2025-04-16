<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionPlanController extends Controller
{
    /**
     * Afficher la page des plans d'abonnement
     */
    public function index()
    {
        // Accessible à tous les rôles connectés pour voir les plans
        // (si tu veux restreindre, décommente la ligne suivante)
        // if (!$this->isAdminOrSuperAdmin() && !$this->isSimpleUser()) abort(403);

        $tenant = Auth::user()->tenant;
        $plans = Plan::where('active', true)->get();
        $currentSubscription = $tenant->subscriptions()
            ->where('status', 'active')
            ->orWhere('status', 'trialing')
            ->first();

        return view('subscriptions.pricing', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription
        ]);
    }

    /**
     * Créer un nouvel abonnement
     */
    public function create(Request $request)
    {
        // Seuls les admins/superadmins peuvent créer un abonnement
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $tenant = Auth::user()->tenant;
        $plan = Plan::findOrFail($request->plan_id);

        // Vérifier si l'utilisateur a déjà un abonnement actif
        $activeSubscription = $tenant->subscriptions()
            ->where('status', 'active')
            ->orWhere('status', 'trialing')
            ->first();

        if ($activeSubscription) {
            return redirect()->back()->with('error', 'Vous avez déjà un abonnement actif.');
        }

        // Rediriger vers la page de paiement avec les informations du plan
        return redirect()->route('subscription.payment', [
            'plan_id' => $plan->id
        ]);
    }

    /**
     * Afficher la page de paiement pour un plan
     */
    public function showPayment(Request $request)
    {
        // Seuls les admins/superadmins peuvent accéder à la page de paiement
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        $plan = Plan::findOrFail($request->plan_id);
        $tenant = Auth::user()->tenant;
        $paymentMethods = $tenant->paymentMethods;

        return view('subscriptions.payment', [
            'plan' => $plan,
            'paymentMethods' => $paymentMethods
        ]);
    }
}