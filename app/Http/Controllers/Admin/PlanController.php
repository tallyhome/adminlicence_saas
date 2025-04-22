<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    /**
     * Display a listing of the plans.
     */
    public function index()
    {
        $plans = Plan::orderBy('price')->get();
        
        // Récupérer les abonnements actifs si l'utilisateur est admin
        $subscriptions = null;
        if (Auth::guard('admin')->check()) {
            if (Auth::guard('admin')->user()->is_super_admin) {
                // Superadmin voit tous les abonnements
                $subscriptions = \App\Models\Subscription::with(['user', 'plan'])->get();
            } else {
                // Admin normal voit uniquement les abonnements de ses utilisateurs
                $userIds = \App\Models\User::where('admin_id', Auth::guard('admin')->id())->pluck('id');
                $subscriptions = \App\Models\Subscription::with(['user', 'plan'])
                    ->whereIn('user_id', $userIds)
                    ->get();
            }
        }
        
        // Vérifier si les services de paiement sont disponibles
        $stripeEnabled = class_exists('Stripe\StripeClient') && 
                         (config('payment.stripe.enabled', false) || 
                          !empty(config('services.stripe.key')));
        
        $paypalEnabled = class_exists('PayPalCheckoutSdk\Core\PayPalHttpClient') && 
                         (config('payment.paypal.enabled', false) || 
                          !empty(config('services.paypal.client_id')));
        
        return view('admin.subscriptions.index', compact('plans', 'subscriptions', 'stripeEnabled', 'paypalEnabled'));
    }

    /**
     * Display a detailed view of subscription plans.
     * 
     * @return \Illuminate\View\View
     */
    public function showPlans()
    {
        // Forcer le rafraîchissement du cache pour obtenir les données les plus récentes
        \Illuminate\Support\Facades\Cache::flush();
        
        // Obtenir les plans directement depuis la base de données pour éviter les problèmes de cache
        $plansData = \Illuminate\Support\Facades\DB::table('plans')
            ->where('is_active', true)
            ->orderBy('price')
            ->get();
            
        // Convertir les données en modèles Plan
        $plans = collect();
        foreach ($plansData as $planData) {
            $plan = new Plan();
            $plan->id = $planData->id;
            $plan->name = $planData->name;
            $plan->slug = $planData->slug;
            $plan->description = $planData->description;
            $plan->price = $planData->price;
            $plan->billing_cycle = $planData->billing_cycle;
            $plan->features = json_decode($planData->features);
            $plan->is_active = $planData->is_active;
            $plan->stripe_price_id = $planData->stripe_price_id;
            $plan->paypal_plan_id = $planData->paypal_plan_id;
            $plan->trial_days = $planData->trial_days;
            $plan->max_licenses = $planData->max_licenses;
            $plan->max_projects = $planData->max_projects;
            $plan->max_clients = $planData->max_clients;
            $plan->created_at = $planData->created_at;
            $plan->updated_at = $planData->updated_at;
            $plans->push($plan);
        }
        
        // Vérifier si les services de paiement sont disponibles
        $stripeEnabled = class_exists('Stripe\StripeClient') && 
                         (config('payment.stripe.enabled', false) || 
                          !empty(config('services.stripe.key')));
        
        $paypalEnabled = class_exists('PayPalCheckoutSdk\Core\PayPalHttpClient') && 
                         (config('payment.paypal.enabled', false) || 
                          !empty(config('services.paypal.client_id')));
        
        // Si aucun plan n'existe et que nous sommes en environnement local, créer des plans par défaut
        if ($plans->isEmpty() && config('app.env') === 'local') {
            $this->createDefaultPlans();
            
            // Récupérer à nouveau les plans après la création des plans par défaut
            $plansData = \Illuminate\Support\Facades\DB::table('plans')
                ->where('is_active', true)
                ->orderBy('price')
                ->get();
                
            // Convertir les données en modèles Plan
            $plans = collect();
            foreach ($plansData as $planData) {
                $plan = new Plan();
                $plan->id = $planData->id;
                $plan->name = $planData->name;
                $plan->slug = $planData->slug;
                $plan->description = $planData->description;
                $plan->price = $planData->price;
                $plan->billing_cycle = $planData->billing_cycle;
                $plan->features = json_decode($planData->features);
                $plan->is_active = $planData->is_active;
                $plan->stripe_price_id = $planData->stripe_price_id;
                $plan->paypal_plan_id = $planData->paypal_plan_id;
                $plan->trial_days = $planData->trial_days;
                $plan->max_licenses = $planData->max_licenses;
                $plan->max_projects = $planData->max_projects;
                $plan->max_clients = $planData->max_clients;
                $plan->created_at = $planData->created_at;
                $plan->updated_at = $planData->updated_at;
                $plans->push($plan);
            }
        }
        
        return view('admin.subscriptions.plans', compact('plans', 'stripeEnabled', 'paypalEnabled'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        return view('admin.subscriptions.create');
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'required|array',
            'features.*' => 'required|string',
            'stripe_price_id' => 'nullable|string|max:255',
            'paypal_plan_id' => 'nullable|string|max:255',
            'trial_days' => 'required|integer|min:0',
            'max_projects' => 'required|integer|min:0',
            'max_licenses' => 'required|integer|min:0',
            'max_products' => 'required|integer|min:0',
            'max_product_licenses' => 'required|integer|min:0',
            'max_apis' => 'required|integer|min:0',
            'max_api_keys' => 'required|integer|min:0',
            'has_api_access' => 'nullable|boolean'
        ]);
        
        $plan = new Plan();
        $plan->name = $request->name;
        $plan->slug = Str::slug($request->name);
        $plan->description = $request->description;
        $plan->price = $request->price;
        $plan->billing_cycle = $request->billing_cycle;
        $plan->features = $request->features;
        $plan->is_active = $request->has('is_active');
        $plan->stripe_price_id = $request->stripe_price_id;
        $plan->paypal_plan_id = $request->paypal_plan_id;
        $plan->trial_days = $request->trial_days;
        $plan->max_projects = $request->max_projects;
        $plan->max_licenses = $request->max_licenses;
        $plan->max_products = $request->max_products;
        $plan->max_product_licenses = $request->max_product_licenses;
        $plan->max_apis = $request->max_apis;
        $plan->max_api_keys = $request->max_api_keys;
        $plan->has_api_access = $request->has('has_api_access');
        $plan->save();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan créé avec succès.');
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit($id)
    {
        $plan = Plan::find($id);
        
        if (!$plan) {
            return redirect()->route('admin.subscriptions.plans')
                ->with('error', 'Le plan demandé n\'existe pas.');
        }
        
        return view('admin.subscriptions.edit', compact('plan'));
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Valider les données
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'billing_cycle' => 'required|in:monthly,yearly',
                'features' => 'required|array',
                'features.*' => 'required|string',
                'stripe_price_id' => 'nullable|string|max:255',
                'paypal_plan_id' => 'nullable|string|max:255',
                'trial_days' => 'required|integer|min:0',
                'max_projects' => 'required|integer|min:0',
                'max_licenses' => 'required|integer|min:0',
                'max_products' => 'required|integer|min:0',
                'max_product_licenses' => 'required|integer|min:0',
                'max_apis' => 'required|integer|min:0',
                'max_api_keys' => 'required|integer|min:0',
                'has_api_access' => 'nullable|boolean'
            ]);
            
            // Récupérer le plan à mettre à jour
            $plan = Plan::findOrFail($id);
            
            // Mettre à jour les champs du plan
            $plan->name = $request->name;
            $plan->description = $request->description;
            $plan->price = $request->price;
            $plan->billing_cycle = $request->billing_cycle;
            $plan->features = $request->features;
            $plan->is_active = $request->has('is_active');
            $plan->stripe_price_id = $request->stripe_price_id;
            $plan->paypal_plan_id = $request->paypal_plan_id;
            $plan->trial_days = $request->trial_days;
            $plan->max_projects = $request->max_projects;
            $plan->max_licenses = $request->max_licenses;
            $plan->max_products = $request->max_products;
            $plan->max_product_licenses = $request->max_product_licenses;
            $plan->max_apis = $request->max_apis;
            $plan->max_api_keys = $request->max_api_keys;
            $plan->has_api_access = $request->has('has_api_access');
            
            // Enregistrer les modifications
            $plan->save();
            
            // Vider le cache pour s'assurer que les modifications sont visibles immédiatement
            \Illuminate\Support\Facades\Cache::flush();
            
            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Plan mis à jour avec succès.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur lors de la mise à jour du plan: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du plan : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy($id)
    {
        $plan = Plan::find($id);
        
        if (!$plan) {
            return redirect()->route('admin.subscriptions.plans')
                ->with('error', 'Le plan demandé n\'existe pas.');
        }
        
        // Vérifier si le plan a des abonnements actifs
        if ($plan->subscriptions()->count() > 0) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Impossible de supprimer un plan avec des abonnements actifs.');
        }

        $plan->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan supprimé avec succès.');
    }
    
    /**
     * Créer des plans par défaut pour démonstration.
     */
    public function createDefaultPlans()
    {
        // Vérifier si l'utilisateur est superadmin
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->back()->with('error', 'Seuls les superadmins peuvent créer des plans par défaut.');
        }
        
        // Créer le plan Basic
        Plan::updateOrCreate(
            ['slug' => 'basic'],
            [
                'name' => 'Basique',
                'description' => 'Plan de base pour les petites équipes',
                'price' => 9.99,
                'billing_cycle' => 'monthly',
                'features' => ['5 projets', '10 licences projet', '5 produits', 'Support standard'],
                'is_active' => true,
                'stripe_price_id' => 'price_basic',
                'paypal_plan_id' => 'P-BASIC',
                'trial_days' => 14,
                'max_licenses' => 10,
                'max_projects' => 5,
                'max_products' => 5,
                'max_product_licenses' => 10,
                'max_apis' => 0,
                'max_api_keys' => 0,
                'has_api_access' => false
            ]
        );
        
        // Créer le plan Pro
        Plan::updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'description' => 'Plan professionnel pour PME',
                'price' => 19.99,
                'billing_cycle' => 'monthly',
                'features' => ['20 projets', '50 licences projet', '20 produits', '30 licences produit', 'Support premium', 'Accès API basique'],
                'is_active' => true,
                'stripe_price_id' => 'price_pro',
                'paypal_plan_id' => 'P-PRO',
                'trial_days' => 7,
                'max_licenses' => 50,
                'max_projects' => 20,
                'max_products' => 20,
                'max_product_licenses' => 30,
                'max_apis' => 2,
                'max_api_keys' => 5,
                'has_api_access' => true
            ]
        );
        
        // Créer le plan Enterprise
        Plan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise',
                'description' => 'Plan entreprise pour grandes sociétés',
                'price' => 49.99,
                'billing_cycle' => 'monthly',
                'features' => ['Projets illimités', 'Licences projet illimitées', 'Produits illimités', 'Licences produit illimitées', 'APIs illimitées', 'Support prioritaire 24/7', 'Accès API complet', 'Personnalisation'],
                'is_active' => true,
                'stripe_price_id' => 'price_enterprise',
                'paypal_plan_id' => 'P-ENTERPRISE',
                'trial_days' => 0,
                'max_licenses' => 0,
                'max_projects' => 0,
                'max_products' => 0,
                'max_product_licenses' => 0,
                'max_apis' => 0,
                'max_api_keys' => 0,
                'has_api_access' => true
            ]
        );
        
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plans par défaut créés avec succès.');
    }
}
