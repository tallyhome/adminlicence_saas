<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\StripeService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $stripeService;
    protected $paypalService;
    
    /**
     * Create a new controller instance.
     */
    public function __construct(StripeService $stripeService, PayPalService $paypalService)
    {
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
    }
    
    /**
     * Affiche la liste des plans d'abonnement
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $plans = Plan::all();
        return view('admin.subscriptions.index', compact('plans'));
    }
    
    /**
     * Affiche le formulaire de création d'un plan
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.subscriptions.create');
    }
    
    /**
     * Enregistre un nouveau plan d'abonnement
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'required|array',
            'stripe_price_id' => 'nullable|string',
            'paypal_plan_id' => 'nullable|string',
            'trial_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);
        
        $plan = new Plan();
        $plan->name = $request->name;
        $plan->description = $request->description;
        $plan->price = $request->price;
        $plan->billing_cycle = $request->billing_cycle;
        $plan->features = json_encode($request->features);
        $plan->stripe_price_id = $request->stripe_price_id;
        $plan->paypal_plan_id = $request->paypal_plan_id;
        $plan->trial_days = $request->trial_days ?? 0;
        $plan->is_active = $request->has('is_active');
        $plan->save();
        
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan d\'abonnement créé avec succès');
    }
    
    /**
     * Affiche le formulaire d'édition d'un plan
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\View\View
     */
    public function edit(Plan $plan)
    {
        return view('admin.subscriptions.edit', compact('plan'));
    }
    
    /**
     * Met à jour un plan d'abonnement
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'required|array',
            'stripe_price_id' => 'nullable|string',
            'paypal_plan_id' => 'nullable|string',
            'trial_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);
        
        $plan->name = $request->name;
        $plan->description = $request->description;
        $plan->price = $request->price;
        $plan->billing_cycle = $request->billing_cycle;
        $plan->features = json_encode($request->features);
        $plan->stripe_price_id = $request->stripe_price_id;
        $plan->paypal_plan_id = $request->paypal_plan_id;
        $plan->trial_days = $request->trial_days ?? 0;
        $plan->is_active = $request->has('is_active');
        $plan->save();
        
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan d\'abonnement mis à jour avec succès');
    }
    
    /**
     * Supprime un plan d'abonnement
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Plan $plan)
    {
        // Vérifier si des abonnements utilisent ce plan
        $subscriptionsCount = Subscription::where('plan_id', $plan->id)->count();
        
        if ($subscriptionsCount > 0) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Ce plan ne peut pas être supprimé car il est utilisé par des abonnements actifs');
        }
        
        $plan->delete();
        
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan d\'abonnement supprimé avec succès');
    }
    
    /**
     * Affiche la liste des abonnements
     *
     * @return \Illuminate\View\View
     */
    public function subscriptions()
    {
        $subscriptions = Subscription::with(['tenant', 'plan'])->paginate(15);
        return view('admin.subscriptions.subscriptions', compact('subscriptions'));
    }
    
    /**
     * Affiche les détails d'un abonnement
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\View\View
     */
    public function showSubscription(Subscription $subscription)
    {
        $subscription->load(['tenant', 'plan', 'paymentMethod', 'invoices']);
        return view('admin.subscriptions.show_subscription', compact('subscription'));
    }
    
    /**
     * Affiche la liste des méthodes de paiement
     *
     * @return \Illuminate\View\View
     */
    public function paymentMethods()
    {
        $stripeCount = PaymentMethod::where('provider', PaymentMethod::PROVIDER_STRIPE)->count();
        $paypalCount = PaymentMethod::where('provider', PaymentMethod::PROVIDER_PAYPAL)->count();
        
        $paymentMethods = PaymentMethod::with('tenant')->paginate(15);
        
        return view('admin.subscriptions.payment_methods', compact('paymentMethods', 'stripeCount', 'paypalCount'));
    }
    
    /**
     * Affiche la liste des factures
     *
     * @return \Illuminate\View\View
     */
    public function invoices()
    {
        $invoices = Invoice::with(['tenant', 'subscription', 'paymentMethod'])->paginate(15);
        return view('admin.subscriptions.invoices', compact('invoices'));
    }
    
    /**
     * Affiche les détails d'une facture
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\View\View
     */
    public function showInvoice(Invoice $invoice)
    {
        $invoice->load(['tenant', 'subscription', 'paymentMethod', 'items']);
        return view('admin.subscriptions.show_invoice', compact('invoice'));
    }
    
    /**
     * Affiche les paramètres de paiement
     *
     * @return \Illuminate\View\View
     */
    public function paymentSettings()
    {
        $stripeSettings = [
            'key' => config('services.stripe.key'),
            'secret' => config('services.stripe.secret'),
            'webhook_secret' => config('services.stripe.webhook_secret'),
        ];
        
        $paypalSettings = [
            'client_id' => config('services.paypal.client_id'),
            'secret' => config('services.paypal.secret'),
            'webhook_id' => config('services.paypal.webhook_id'),
            'sandbox' => config('services.paypal.sandbox'),
        ];
        
        return view('admin.subscriptions.payment_settings', compact('stripeSettings', 'paypalSettings'));
    }
    
    /**
     * Met à jour les paramètres de paiement
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePaymentSettings(Request $request)
    {
        $request->validate([
            'stripe_key' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'stripe_webhook_secret' => 'nullable|string',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string',
            'paypal_webhook_id' => 'nullable|string',
            'paypal_sandbox' => 'boolean',
        ]);
        
        // Mettre à jour le fichier .env avec les nouvelles valeurs
        $this->updateEnvFile([
            'STRIPE_KEY' => $request->stripe_key,
            'STRIPE_SECRET' => $request->stripe_secret,
            'STRIPE_WEBHOOK_SECRET' => $request->stripe_webhook_secret,
            'PAYPAL_CLIENT_ID' => $request->paypal_client_id,
            'PAYPAL_SECRET' => $request->paypal_secret,
            'PAYPAL_WEBHOOK_ID' => $request->paypal_webhook_id,
            'PAYPAL_SANDBOX' => $request->has('paypal_sandbox') ? 'true' : 'false',
        ]);
        
        return redirect()->route('admin.subscriptions.payment-settings')
            ->with('success', 'Paramètres de paiement mis à jour avec succès');
    }
    
    /**
     * Met à jour le fichier .env avec les nouvelles valeurs
     *
     * @param  array  $values
     * @return void
     */
    private function updateEnvFile(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                if ($envValue) {
                    $str = preg_replace(
                        "/^{$envKey}=.*/m",
                        "{$envKey}={$envValue}",
                        $str
                    );
                    
                    // Si la clé n'existe pas, l'ajouter à la fin du fichier
                    if (!preg_match("/^{$envKey}=/m", $str)) {
                        $str .= "\n{$envKey}={$envValue}";
                    }
                }
            }
        }
        
        file_put_contents($envFile, $str);
    }
}