<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\StripeService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Display the subscription plans page.
     *
     * @return \Illuminate\View\View
     */
    public function plans()
    {
        // Vérifie si l'utilisateur est connecté
        if (!Auth::check() && !Auth::guard('admin')->check()) {
            return redirect()->route('login');
        }
        
        // Rediriger vers la nouvelle page d'abonnements dans le dashboard admin
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.subscriptions.index');
        } else {
            // Pour les utilisateurs normaux, récupérer les plans disponibles
            $plans = \App\Models\Plan::where('is_active', true)->get();
            $user = Auth::user();
            
            return view('subscription.plans', [
                'plans' => $plans,
                'user' => $user
            ]);
        }
    }
    
    /**
     * Display the checkout page for a specific plan.
     *
     * @param  string  $planId
     * @return \Illuminate\View\View
     */
    public function checkout($planId)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check() && !Auth::guard('admin')->check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour souscrire à un abonnement.');
        }
        
        // Récupérer le plan depuis la base de données
        $plan = \App\Models\Plan::findOrFail($planId);
        
        if (!$plan->is_active) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Ce plan n\'est pas disponible actuellement.');
        }
        
        // Récupérer les méthodes de paiement disponibles
        $stripeEnabled = config('payment.stripe.enabled', false);
        $paypalEnabled = config('payment.paypal.enabled', false);
        
        // Récupérer les méthodes de paiement enregistrées de l'utilisateur si disponible
        $paymentMethods = [];
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->stripe_id) {
                // Récupérer les méthodes de paiement Stripe
                $paymentMethods = $this->stripeService->getPaymentMethods($user->stripe_id);
            }
        }
        
        return view('subscription.checkout', compact('plan', 'stripeEnabled', 'paypalEnabled', 'paymentMethods'));
    }
    
    /**
     * Process a Stripe subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processStripeSubscription(Request $request)
    {
        // Seulement user simple peut souscrire
        if (!$this->isSimpleUser()) abort(403);
        
        $request->validate([
            'plan_id' => 'required|string',
            'payment_method_id' => 'required|string',
            'trial_days' => 'nullable|integer|min:0',
        ]);
        
        try {
            // Get the current tenant
            $tenant = Auth::user()->tenant;
            
            // Get the selected plan
            $plans = config('subscription.plans');
            $plan = collect($plans)->firstWhere('id', $request->plan_id);
            
            if (!$plan) {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Selected plan not found.');
            }
            
            // Create Stripe customer if not exists
            if (!$tenant->stripe_customer_id) {
                $customerId = $this->stripeService->createCustomer($tenant);
                
                if (!$customerId) {
                    return redirect()->back()
                        ->with('error', 'Failed to create customer in Stripe.');
                }
                
                $tenant->stripe_customer_id = $customerId;
                $tenant->save();
            }
            
            // Create or retrieve payment method
            $paymentMethod = null;
            
            if ($request->has('existing_payment_method_id')) {
                // Use existing payment method
                $paymentMethod = PaymentMethod::find($request->existing_payment_method_id);
                
                if (!$paymentMethod || $paymentMethod->tenant_id !== $tenant->id) {
                    return redirect()->back()
                        ->with('error', 'Invalid payment method.');
                }
            } else {
                // Create new payment method
                $paymentMethod = $this->stripeService->createPaymentMethod(
                    $tenant,
                    $request->payment_method_id
                );
                
                if (!$paymentMethod) {
                    return redirect()->back()
                        ->with('error', 'Failed to create payment method.');
                }
            }
            
            // Create subscription
            $trialDays = $request->trial_days ?? 0;
            $subscription = $this->stripeService->createSubscription(
                $tenant,
                $plan['stripe_price_id'],
                $paymentMethod,
                $trialDays
            );
            
            if (!$subscription) {
                return redirect()->back()
                    ->with('error', 'Failed to create subscription.');
            }
            
            return redirect()->route('subscription.success')
                ->with('success', 'Subscription created successfully.');
        } catch (\Exception $e) {
            Log::error('Stripe subscription error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Process a PayPal subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayPalSubscription(Request $request)
    {
        // Seulement user simple peut souscrire
        if (!$this->isSimpleUser()) abort(403);
        
        $request->validate([
            'plan_id' => 'required|string',
            'paypal_email' => 'required|email',
            'trial_days' => 'nullable|integer|min:0',
        ]);
        
        try {
            // Get the current tenant
            $tenant = Auth::user()->tenant;
            
            // Get the selected plan
            $plans = config('subscription.plans');
            $plan = collect($plans)->firstWhere('id', $request->plan_id);
            
            if (!$plan) {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Selected plan not found.');
            }
            
            // Create PayPal customer reference
            $this->paypalService->createCustomer($tenant);
            
            // Create or retrieve payment method
            $paymentMethod = null;
            
            if ($request->has('existing_payment_method_id')) {
                // Use existing payment method
                $paymentMethod = PaymentMethod::find($request->existing_payment_method_id);
                
                if (!$paymentMethod || $paymentMethod->tenant_id !== $tenant->id) {
                    return redirect()->back()
                        ->with('error', 'Invalid payment method.');
                }
            } else {
                // Create new payment method
                $paymentMethod = $this->paypalService->createPaymentMethod(
                    $tenant,
                    $request->paypal_email
                );
                
                if (!$paymentMethod) {
                    return redirect()->back()
                        ->with('error', 'Failed to create payment method.');
                }
            }
            
            // Create subscription
            $trialDays = $request->trial_days ?? 0;
            $subscription = $this->paypalService->createSubscription(
                $tenant,
                $plan['paypal_plan_id'],
                $paymentMethod,
                $trialDays
            );
            
            if (!$subscription) {
                return redirect()->back()
                    ->with('error', 'Failed to create subscription.');
            }
            
            return redirect()->route('subscription.success')
                ->with('success', 'Subscription created successfully.');
        } catch (\Exception $e) {
            Log::error('PayPal subscription error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the payment methods page.
     *
     * @return \Illuminate\View\View
     */
    public function paymentMethods()
    {
        // Accessible à tous les rôles connectés
        
        // Get the current tenant
        $tenant = Auth::user()->tenant;
        
        // Get payment methods
        $paymentMethods = $tenant->paymentMethods;
        
        return view('subscription.payment-methods', compact('tenant', 'paymentMethods'));
    }
    
    /**
     * Display the form to add a new payment method.
     *
     * @param  string  $type
     * @return \Illuminate\View\View
     */
    public function addPaymentMethod($type = 'card')
    {
        // Accessible à tous les rôles connectés
        
        // Get the current tenant
        $tenant = Auth::user()->tenant;
        
        return view('subscription.add-payment-method', compact('tenant', 'type'));
    }
    
    /**
     * Store a new Stripe payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeStripePaymentMethod(Request $request)
    {
        // Accessible à tous les rôles connectés
        
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);
        
        try {
            // Get the current tenant
            $tenant = Auth::user()->tenant;
            
            // Create Stripe customer if not exists
            if (!$tenant->stripe_customer_id) {
                $customerId = $this->stripeService->createCustomer($tenant);
                
                if (!$customerId) {
                    return redirect()->back()
                        ->with('error', 'Failed to create customer in Stripe.');
                }
                
                $tenant->stripe_customer_id = $customerId;
                $tenant->save();
            }
            
            // Create payment method
            $paymentMethod = $this->stripeService->createPaymentMethod(
                $tenant,
                $request->payment_method_id
            );
            
            if (!$paymentMethod) {
                return redirect()->back()
                    ->with('error', 'Failed to create payment method.');
            }
            
            return redirect()->route('subscription.payment-methods')
                ->with('success', 'Payment method added successfully.');
        } catch (\Exception $e) {
            Log::error('Stripe payment method error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a new PayPal payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePayPalPaymentMethod(Request $request)
    {
        // Accessible à tous les rôles connectés
        
        $request->validate([
            'paypal_email' => 'required|email',
        ]);
        
        try {
            // Get the current tenant
            $tenant = Auth::user()->tenant;
            
            // Create payment method
            $paymentMethod = $this->paypalService->createPaymentMethod(
                $tenant,
                $request->paypal_email
            );
            
            if (!$paymentMethod) {
                return redirect()->back()
                    ->with('error', 'Failed to create payment method.');
            }
            
            return redirect()->route('subscription.payment-methods')
                ->with('success', 'Payment method added successfully.');
        } catch (\Exception $e) {
            Log::error('PayPal payment method error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Set a payment method as default.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setDefaultPaymentMethod($id)
    {
        // Accessible à tous les rôles connectés
        
        try {
            // Get the current tenant
            $tenant = Auth::user()->tenant;
            
            // Get the payment method
            $paymentMethod = PaymentMethod::find($id);
            
            if (!$paymentMethod || $paymentMethod->tenant_id !== $tenant->id) {
                return redirect()->back()
                    ->with('error', 'Invalid payment method.');
            }
            
            // Set all payment methods as non-default
            PaymentMethod::where('tenant_id', $tenant->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
            
            // Set the selected payment method as default
            $paymentMethod->is_default = true;
            $paymentMethod->save();
            
            return redirect()->route('subscription.payment-methods')
                ->with('success', 'Default payment method updated successfully.');
        } catch (\Exception $e) {
            Log::error('Set default payment method error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a payment method.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePaymentMethod($id)
    {
        // Accessible à tous les rôles connectés
        
        try {
            // Get the current tenant
            $tenant = Auth::user()->tenant;
            
            // Get the payment method
            $paymentMethod = PaymentMethod::find($id);
            
            if (!$paymentMethod || $paymentMethod->tenant_id !== $tenant->id) {
                return redirect()->back()
                    ->with('error', 'Invalid payment method.');
            }
            
            // Check if it's the only payment method
            $count = PaymentMethod::where('tenant_id', $tenant->id)->count();
            
            if ($count <= 1) {
                return redirect()->back()
                    ->with('error', 'Cannot delete the only payment method.');
            }
            
            // Check if it's used by an active subscription
            $activeSubscription = Subscription::where('tenant_id', $tenant->id)
                ->where('payment_method_id', $id)
                ->whereNull('ends_at')
                ->first();
            
            if ($activeSubscription) {
                return redirect()->back()
                    ->with('error', 'Cannot delete a payment method used by an active subscription.');
            }
            
            // Delete the payment method
            $paymentMethod->delete();
            
            return redirect()->route('subscription.payment-methods')
                ->with('success', 'Payment method deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Delete payment method error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the invoices page.
     *
     * @return \Illuminate\View\View
     */
    public function invoices()
    {
        // Accessible à tous les rôles connectés
        
        // Get the current tenant
        $tenant = Auth::user()->tenant;
        
        // Get invoices
        $invoices = $tenant->invoices()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('subscription.invoices', compact('tenant', 'invoices'));
    }
    
    /**
     * Display a specific invoice.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showInvoice($id)
    {
        // Accessible à tous les rôles connectés
        
        // Get the current tenant
        $tenant = Auth::user()->tenant;
        
        // Get the invoice
        $invoice = Invoice::find($id);
        
        if (!$invoice || $invoice->tenant_id !== $tenant->id) {
            return redirect()->route('subscription.invoices')
                ->with('error', 'Invoice not found.');
        }
        
        return view('subscription.invoice-details', compact('tenant', 'invoice'));
    }
    
    /**
     * Cancel the current subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubscription(Request $request)
    {
        // Seuls les admins/superadmins peuvent annuler
        if (!$this->isAdminOrSuperAdmin()) abort(403);
        
        $request->validate([
            'at_period_end' => 'nullable|boolean',
        ]);
        
        try {
            // Get the current tenant
            $tenant = Auth::user()->tenant;
            
            // Get the current subscription
            $subscription = $tenant->subscriptions()->whereNull('ends_at')->first();
            
            if (!$subscription) {
                return redirect()->back()
                    ->with('error', 'No active subscription found.');
            }
            
            // Determine if cancellation should be immediate or at period end
            $atPeriodEnd = $request->has('at_period_end') ? (bool) $request->at_period_end : true;
            
            // Cancel the subscription based on the payment method type
            $success = false;
            
            if ($subscription->payment_method_type === PaymentMethod::PROVIDER_STRIPE) {
                $success = $this->stripeService->cancelSubscription($subscription, $atPeriodEnd);
            } elseif ($subscription->payment_method_type === PaymentMethod::PROVIDER_PAYPAL) {
                $success = $this->paypalService->cancelSubscription($subscription, $atPeriodEnd);
            }
            
            if (!$success) {
                return redirect()->back()
                    ->with('error', 'Failed to cancel subscription.');
            }
            
            // Rediriger vers le dashboard approprié en fonction du rôle
            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Abonnement annulé avec succès.');
            } else {
                return redirect()->route('subscription.plans')
                    ->with('success', 'Abonnement annulé avec succès.');
            }
        } catch (\Exception $e) {
            Log::error('Cancel subscription error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Resume a cancelled subscription.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resumeSubscription()
    {
        // Seuls les admins/superadmins peuvent reprendre
        if (!$this->isAdminOrSuperAdmin()) abort(403);
        
        try {
            // Get the current tenant
            $tenant = Auth::user()->tenant;
            
            // Get the current subscription
            $subscription = $tenant->subscriptions()
                ->where('ends_at', '>', now())
                ->first();
            
            if (!$subscription) {
                return redirect()->back()
                    ->with('error', 'No cancellable subscription found.');
            }
            
            // Resume the subscription based on the payment method type
            if ($subscription->payment_method_type === PaymentMethod::PROVIDER_STRIPE) {
                // For Stripe, we can simply update the subscription to remove the cancel_at_period_end flag
                $this->stripe->subscriptions->update($subscription->stripe_id, [
                    'cancel_at_period_end' => false,
                ]);
                
                $subscription->ends_at = null;
                $subscription->save();
                
                // Update tenant subscription information
                $tenant->subscription_status = Tenant::SUBSCRIPTION_ACTIVE;
                $tenant->save();
            } else {
                // For PayPal, we might need to create a new subscription as PayPal doesn't support resuming
                return redirect()->back()
                    ->with('error', 'Resuming PayPal subscriptions is not supported. Please create a new subscription.');
            }
            
            // Rediriger vers le dashboard approprié en fonction du rôle
            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Abonnement réactivé avec succès.');
            } else {
                return redirect()->route('subscription.plans')
                    ->with('success', 'Abonnement réactivé avec succès.');
            }
        } catch (\Exception $e) {
            Log::error('Resume subscription error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche le tableau de bord de test des paiements.
     *
     * @return \Illuminate\View\View
     */
    public function paymentTestDashboard()
    {
        // Vérifier si l'utilisateur est un super-admin
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->is_super_admin) {
            abort(403, 'Accès non autorisé');
        }
        
        // Récupérer les informations de configuration des passerelles de paiement
        $stripeEnabled = config('payment.stripe.enabled', false);
        $stripeKey = config('payment.stripe.key');
        $stripeSecret = config('payment.stripe.secret');
        
        $paypalEnabled = config('payment.paypal.enabled', false);
        $paypalClientId = config('payment.paypal.client_id');
        $paypalSecret = config('payment.paypal.secret');
        
        // Récupérer les dernières factures pour afficher les résultats des tests
        $recentInvoices = Invoice::orderBy('created_at', 'desc')->take(5)->get();
        
        return view('admin.payment-test', compact(
            'stripeEnabled', 'stripeKey', 'stripeSecret',
            'paypalEnabled', 'paypalClientId', 'paypalSecret',
            'recentInvoices'
        ));
    }
    
    /**
     * Teste un paiement Stripe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testStripePayment(Request $request)
    {
        // Vérifier si l'utilisateur est un super-admin
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->is_super_admin) {
            abort(403, 'Accès non autorisé');
        }
        
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'description' => 'required|string|max:255',
        ]);
        
        try {
            // Créer une facture de test
            $invoice = new Invoice();
            $invoice->tenant_id = Auth::guard('admin')->user()->id; // Utiliser l'ID de l'admin comme tenant_id pour le test
            $invoice->number = 'TEST-' . time();
            $invoice->total = $request->amount * 100; // Convertir en centimes
            $invoice->currency = strtolower($request->currency);
            $invoice->status = Invoice::STATUS_PAID;
            $invoice->billing_reason = 'test_payment';
            $invoice->provider = Invoice::PROVIDER_STRIPE;
            $invoice->provider_id = 'test_' . uniqid();
            $invoice->paid_at = now();
            $invoice->save();
            
            // Simuler un événement de paiement réussi
            $this->stripeService->handleInvoicePaymentSucceeded((object) [
                'id' => $invoice->provider_id,
                'subscription' => null,
                'number' => $invoice->number,
                'total' => $invoice->total,
                'currency' => $invoice->currency,
                'billing_reason' => $invoice->billing_reason,
                'due_date' => now()->timestamp,
                'lines' => (object) [
                    'data' => [
                        (object) [
                            'description' => $request->description,
                            'amount' => $invoice->total,
                            'quantity' => 1,
                            'period' => (object) [
                                'start' => now()->timestamp,
                                'end' => now()->addMonth()->timestamp,
                            ],
                            'type' => 'invoice_item',
                        ],
                    ],
                ],
            ]);
            
            return redirect()->route('admin.payment-test')
                ->with('success', 'Test de paiement Stripe effectué avec succès. Facture #' . $invoice->number . ' créée.');
        } catch (\Exception $e) {
            Log::error('Test Stripe payment error: ' . $e->getMessage());
            
            return redirect()->route('admin.payment-test')
                ->with('error', 'Erreur lors du test de paiement Stripe: ' . $e->getMessage());
        }
    }
    
    /**
     * Teste un paiement PayPal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testPayPalPayment(Request $request)
    {
        // Vérifier si l'utilisateur est un super-admin
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->is_super_admin) {
            abort(403, 'Accès non autorisé');
        }
        
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'description' => 'required|string|max:255',
        ]);
        
        try {
            // Créer une facture de test
            $invoice = new Invoice();
            $invoice->tenant_id = Auth::guard('admin')->user()->id; // Utiliser l'ID de l'admin comme tenant_id pour le test
            $invoice->number = 'TEST-PAYPAL-' . time();
            $invoice->total = $request->amount * 100; // Convertir en centimes
            $invoice->currency = strtolower($request->currency);
            $invoice->status = Invoice::STATUS_PAID;
            $invoice->billing_reason = 'test_payment';
            $invoice->provider = Invoice::PROVIDER_PAYPAL;
            $invoice->provider_id = 'test_paypal_' . uniqid();
            $invoice->paid_at = now();
            $invoice->save();
            
            // Simuler un événement de paiement réussi
            $this->paypalService->handlePaymentCompleted([
                'resource' => [
                    'id' => $invoice->provider_id,
                    'billing_agreement_id' => null,
                    'amount' => [
                        'total' => $request->amount,
                        'currency' => strtoupper($request->currency),
                    ],
                ],
            ]);
            
            return redirect()->route('admin.payment-test')
                ->with('success', 'Test de paiement PayPal effectué avec succès. Facture #' . $invoice->number . ' créée.');
        } catch (\Exception $e) {
            Log::error('Test PayPal payment error: ' . $e->getMessage());
            
            return redirect()->route('admin.payment-test')
                ->with('error', 'Erreur lors du test de paiement PayPal: ' . $e->getMessage());
        }
    }
}