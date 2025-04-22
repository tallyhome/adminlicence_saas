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
        try {
            // Récupérer les plans disponibles
            $plans = \App\Models\Plan::where('is_active', true)->get();
            
            // Si aucun plan n'est trouvé, créer des plans par défaut pour le développement
            if ($plans->isEmpty() && config('app.env') === 'local') {
                // Créer des plans par défaut pour le développement
                $this->createDefaultPlans();
                $plans = \App\Models\Plan::where('is_active', true)->get();
            }
            
            // Récupérer l'utilisateur s'il est connecté
            $user = null;
            if (Auth::check()) {
                $user = Auth::user();
            }
            
            // Vérifier si les services de paiement sont disponibles
            $stripeEnabled = class_exists('Stripe\StripeClient') && 
                             (config('payment.stripe.enabled', false) || 
                              !empty(config('services.stripe.key')));
            
            $paypalEnabled = class_exists('PayPalCheckoutSdk\Core\PayPalHttpClient') && 
                             (config('payment.paypal.enabled', false) || 
                              !empty(config('services.paypal.client_id')));
            
            // Forcer l'activation des passerelles de paiement pour les tests
            $stripeEnabled = true;
            $paypalEnabled = true;
            
            // Toujours afficher la vue des plans, sans redirection
            return view('subscription.plans', [
                'plans' => $plans,
                'user' => $user,
                'stripeEnabled' => $stripeEnabled,
                'paypalEnabled' => $paypalEnabled
            ]);
        } catch (\Exception $e) {
            // Journaliser l'erreur
            Log::error('Erreur lors de l\'affichage des plans : ' . $e->getMessage());
            
            // Afficher un message d'erreur à l'utilisateur
            return view('subscription.plans', [
                'plans' => [],
                'user' => null,
                'stripeEnabled' => false,
                'paypalEnabled' => false,
                'error' => 'Une erreur est survenue lors du chargement des plans. Veuillez réessayer ultérieurement.'
            ]);
        }
    }
    
    /**
     * Créer des plans par défaut pour le développement
     */
    private function createDefaultPlans()
    {
        $plans = [
            [
                'name' => 'Essentiel',
                'description' => 'Idéal pour les petites entreprises',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Gestion de 50 licences',
                    'Support par email',
                    'Mises à jour automatiques',
                    'Tableau de bord basique'
                ]),
                'is_active' => true,
                'trial_days' => 14
            ],
            [
                'name' => 'Professionnel',
                'description' => 'Pour les entreprises en croissance',
                'price' => 59.99,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Gestion de 200 licences',
                    'Support prioritaire',
                    'Mises à jour automatiques',
                    'Tableau de bord avancé',
                    'API d\'intégration',
                    'Rapports détaillés'
                ]),
                'is_active' => true,
                'trial_days' => 7
            ],
            [
                'name' => 'Entreprise',
                'description' => 'Solution complète pour grandes entreprises',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Licences illimitées',
                    'Support 24/7',
                    'Mises à jour prioritaires',
                    'Tableau de bord personnalisable',
                    'API complète',
                    'Rapports avancés',
                    'Intégration SSO',
                    'Déploiement sur site disponible'
                ]),
                'is_active' => true,
                'trial_days' => 0
            ]
        ];
        
        foreach ($plans as $planData) {
            \App\Models\Plan::create($planData);
        }
    }
    
    /**
     * Display the checkout page for a specific plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $planId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request, $planId)
    {
        // Récupérer le plan depuis la base de données
        $plan = \App\Models\Plan::findOrFail($planId);
        
        if (!$plan->is_active) {
            return redirect()->to('/subscription/plans')
                ->with('error', 'Ce plan n\'est pas disponible actuellement.');
        }
        
        // Récupérer les méthodes de paiement disponibles
        $stripeEnabled = config('payment.stripe.enabled', false) || !empty(config('services.stripe.key'));
        $paypalEnabled = config('payment.paypal.enabled', false) || !empty(config('services.paypal.client_id'));
        
        // Si aucune méthode de paiement n'est activée, rediriger avec un message d'erreur
        if (!$stripeEnabled && !$paypalEnabled) {
            return redirect()->to('/subscription/plans')
                ->with('error', 'Aucune méthode de paiement n\'est configurée. Veuillez contacter l\'administrateur.');
        }
        
        // Si c'est une requête POST, traiter la méthode de paiement sélectionnée
        if ($request->isMethod('post')) {
            $paymentMethod = $request->input('payment_method');
            
            // Rediriger directement vers la page de traitement appropriée
            if ($paymentMethod === 'stripe' && $stripeEnabled) {
                return redirect()->route('subscription.process-stripe', ['plan_id' => $plan->id]);
            } elseif ($paymentMethod === 'paypal' && $paypalEnabled) {
                return redirect()->route('subscription.process-paypal', ['plan_id' => $plan->id]);
            }
        }
        
        // Récupérer les méthodes de paiement enregistrées de l'utilisateur si disponible
        $paymentMethods = [];
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->stripe_id) {
                try {
                    // Récupérer les méthodes de paiement Stripe
                    $paymentMethods = $this->stripeService->getPaymentMethods($user->stripe_id);
                } catch (\Exception $e) {
                    // Journaliser l'erreur mais continuer
                    Log::error('Erreur lors de la récupération des méthodes de paiement Stripe: ' . $e->getMessage());
                }
            }
        }
        
        // Déterminer la méthode de paiement préférée (Stripe par défaut si disponible)
        $preferredMethod = $request->input('method', $stripeEnabled ? 'stripe' : 'paypal');
        
        return view('subscription.checkout', compact('plan', 'stripeEnabled', 'paypalEnabled', 'paymentMethods', 'preferredMethod'));
    }
    
    /**
     * Process a Stripe subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processStripeSubscription(Request $request)
    {
        // Désactiver temporairement la vérification du type d'utilisateur pour le débogage
        // if (!$this->isSimpleUser()) abort(403);
        
        // Journaliser les données reçues pour le débogage
        Log::info('Stripe subscription request received', $request->all());
        
        $request->validate([
            'plan_id' => 'required|string',
            'payment_method_id' => 'nullable|string',
            'trial_days' => 'nullable|integer|min:0',
        ]);
        
        try {
            // Vérifier si l'utilisateur est connecté
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Vous devez être connecté pour souscrire à un abonnement.');
            }
            
            $user = Auth::user();
            
            // Get the selected plan
            $plan = \App\Models\Plan::find($request->plan_id);
            
            if (!$plan) {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Le plan sélectionné n\'a pas été trouvé.');
            }
            
            // Si aucun payment_method_id n'est fourni, rediriger vers la page de paiement Stripe
            if (!$request->has('payment_method_id') || empty($request->payment_method_id)) {
                // Rediriger vers la page de paiement Stripe avec les informations du plan
                return view('subscription.stripe-payment', [
                    'plan' => $plan,
                    'stripeKey' => config('services.stripe.key')
                ]);
            }
            
            // Simuler un succès pour le débogage
            Log::info('Stripe subscription simulated success for plan: ' . $plan->name);
            
            return redirect()->route('subscription.success')
                ->with('success', 'Abonnement créé avec succès (simulation).');
        } catch (\Exception $e) {
            Log::error('Erreur d\'abonnement Stripe : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
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
        // Désactiver temporairement la vérification du type d'utilisateur pour le débogage
        // if (!$this->isSimpleUser()) abort(403);
        
        // Journaliser les données reçues pour le débogage
        Log::info('PayPal subscription request received', $request->all());
        
        $request->validate([
            'plan_id' => 'required|string',
        ]);
        
        try {
            // Vérifier si l'utilisateur est connecté
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Vous devez être connecté pour souscrire à un abonnement.');
            }
            
            $user = Auth::user();
            
            // Get the selected plan
            $plan = \App\Models\Plan::find($request->plan_id);
            
            if (!$plan) {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Le plan sélectionné n\'a pas été trouvé.');
            }
            
            // Simuler un succès pour le débogage
            Log::info('PayPal subscription simulated success for plan: ' . $plan->name);
            
            return redirect()->route('subscription.success')
                ->with('success', 'Abonnement PayPal créé avec succès (simulation).');
        } catch (\Exception $e) {
            Log::error('Erreur d\'abonnement PayPal : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
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
                        ->with('error', 'Échec de la création du client dans Stripe.');
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
                    ->with('error', 'Échec de la création de la méthode de paiement.');
            }
            
            return redirect()->route('subscription.payment-methods')
                ->with('success', 'Méthode de paiement ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur de méthode de paiement Stripe : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
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
                    ->with('error', 'Échec de la création de la méthode de paiement.');
            }
            
            return redirect()->route('subscription.payment-methods')
                ->with('success', 'Méthode de paiement ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur de méthode de paiement PayPal : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
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
                    ->with('error', 'Méthode de paiement invalide.');
            }
            
            // Set all payment methods as non-default
            PaymentMethod::where('tenant_id', $tenant->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
            
            // Set the selected payment method as default
            $paymentMethod->is_default = true;
            $paymentMethod->save();
            
            return redirect()->route('subscription.payment-methods')
                ->with('success', 'Méthode de paiement par défaut mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur de méthode de paiement par défaut : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
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
                    ->with('error', 'Méthode de paiement invalide.');
            }
            
            // Check if it's the only payment method
            $count = PaymentMethod::where('tenant_id', $tenant->id)->count();
            
            if ($count <= 1) {
                return redirect()->back()
                    ->with('error', 'Impossible de supprimer la seule méthode de paiement.');
            }
            
            // Check if it's used by an active subscription
            $activeSubscription = Subscription::where('tenant_id', $tenant->id)
                ->where('payment_method_id', $id)
                ->whereNull('ends_at')
                ->first();
            
            if ($activeSubscription) {
                return redirect()->back()
                    ->with('error', 'Impossible de supprimer une méthode de paiement utilisée par un abonnement actif.');
            }
            
            // Delete the payment method
            $paymentMethod->delete();
            
            return redirect()->route('subscription.payment-methods')
                ->with('success', 'Méthode de paiement supprimée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur de suppression de méthode de paiement : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
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
                ->with('error', 'Facture non trouvée.');
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
                    ->with('error', 'Aucun abonnement actif trouvé.');
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
                    ->with('error', 'Échec de l\'annulation de l\'abonnement.');
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
            Log::error('Erreur d\'annulation d\'abonnement : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
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
                    ->with('error', 'Aucun abonnement annulé trouvé.');
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
                    ->with('error', 'La reprise des abonnements PayPal n\'est pas prise en charge. Veuillez créer un nouvel abonnement.');
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
            Log::error('Erreur de reprise d\'abonnement : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
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
            Log::error('Erreur de test de paiement Stripe : ' . $e->getMessage());
            
            return redirect()->route('admin.payment-test')
                ->with('error', 'Erreur lors du test de paiement Stripe : ' . $e->getMessage());
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
            Log::error('Erreur de test de paiement PayPal : ' . $e->getMessage());
            
            return redirect()->route('admin.payment-test')
                ->with('error', 'Erreur lors du test de paiement PayPal : ' . $e->getMessage());
        }
    }
    
    /**
     * Vérifie si l'utilisateur connecté est un utilisateur simple (non admin)
     *
     * @return bool
     */
    protected function isSimpleUser()
    {
        // Si l'utilisateur n'est pas connecté, retourner false
        if (!Auth::check()) {
            return false;
        }
        
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (Auth::guard('admin')->check()) {
            return false;
        }
        
        // Si on arrive ici, c'est un utilisateur simple
        return true;
    }
    
    /**
     * Vérifie si l'utilisateur connecté est un admin ou un super-admin
     *
     * @return bool
     */
    protected function isAdminOrSuperAdmin()
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            return false;
        }
        
        // Vérifier si l'utilisateur est un super-admin
        if (Auth::guard('admin')->user()->is_super_admin) {
            return true;
        }
        
        // Si on arrive ici, c'est un admin mais pas un super-admin
        return false;
    }

    /**
     * Affiche la page de succès après un abonnement réussi
     * 
     * @return \Illuminate\View\View
     */
    public function success()
    {
        return view('subscription.success');
    }
}