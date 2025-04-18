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
        
        return view('admin.subscriptions.plans', compact('plans', 'subscriptions'));
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
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'trial_days' => 'nullable|integer|min:0',
            'max_licenses' => 'nullable|integer|min:1',
            'max_projects' => 'nullable|integer|min:1',
            'max_clients' => 'nullable|integer|min:1',
            'stripe_price_id' => 'nullable|string',
            'paypal_plan_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $plan = new Plan();
        $plan->name = $request->name;
        $plan->slug = Str::slug($request->name);
        $plan->price = $request->price;
        $plan->billing_cycle = $request->billing_cycle;
        $plan->description = $request->description;
        $plan->features = $request->features;
        $plan->trial_days = $request->trial_days ?? 0;
        $plan->max_licenses = $request->max_licenses ?? 1;
        $plan->max_projects = $request->max_projects ?? 1;
        $plan->max_clients = $request->max_clients ?? 1;
        $plan->stripe_price_id = $request->stripe_price_id;
        $plan->paypal_plan_id = $request->paypal_plan_id;
        $plan->is_active = $request->has('is_active');
        $plan->save();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan créé avec succès.');
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan)
    {
        return view('admin.subscriptions.edit', compact('plan'));
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'trial_days' => 'nullable|integer|min:0',
            'max_licenses' => 'nullable|integer|min:1',
            'max_projects' => 'nullable|integer|min:1',
            'max_clients' => 'nullable|integer|min:1',
            'stripe_price_id' => 'nullable|string',
            'paypal_plan_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $plan->name = $request->name;
        // Ne pas modifier le slug pour éviter les problèmes avec les abonnements existants
        $plan->price = $request->price;
        $plan->billing_cycle = $request->billing_cycle;
        $plan->description = $request->description;
        $plan->features = $request->features;
        $plan->trial_days = $request->trial_days ?? 0;
        $plan->max_licenses = $request->max_licenses ?? 1;
        $plan->max_projects = $request->max_projects ?? 1;
        $plan->max_clients = $request->max_clients ?? 1;
        $plan->stripe_price_id = $request->stripe_price_id;
        $plan->paypal_plan_id = $request->paypal_plan_id;
        $plan->is_active = $request->has('is_active');
        $plan->save();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan mis à jour avec succès.');
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Plan $plan)
    {
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
                'features' => ['5 projets', '10 licences', 'Support standard'],
                'is_active' => true,
                'stripe_price_id' => 'price_basic',
                'paypal_plan_id' => 'P-BASIC',
                'trial_days' => 14,
                'max_licenses' => 10,
                'max_projects' => 5,
                'max_clients' => 10
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
                'features' => ['20 projets', '50 licences', 'Support premium', 'API accès'],
                'is_active' => true,
                'stripe_price_id' => 'price_pro',
                'paypal_plan_id' => 'P-PRO',
                'trial_days' => 7,
                'max_licenses' => 50,
                'max_projects' => 20,
                'max_clients' => 50
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
                'features' => ['Projets illimités', 'Licences illimitées', 'Support prioritaire 24/7', 'API accès', 'Personnalisation'],
                'is_active' => true,
                'stripe_price_id' => 'price_enterprise',
                'paypal_plan_id' => 'P-ENTERPRISE',
                'trial_days' => 0,
                'max_licenses' => 999,
                'max_projects' => 999,
                'max_clients' => 999
            ]
        );
        
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plans par défaut créés avec succès.');
    }
}
