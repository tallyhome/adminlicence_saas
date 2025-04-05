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
        // Get the current tenant
        $tenant = Auth::user()->tenant;
        
        // Get the current subscription if any
        $subscription = $tenant->subscriptions()->first();
        
        // Get available plans (could be fetched from database or config)
        $plans = [
            [
                'id' => 'basic',
                'name' => 'Basic',
                'description' => 'Basic plan for small businesses',
                'price' => 9.99,
                'currency' => 'EUR',
                'features' => [
                    '5 projects',
                    '100 licences',
                    'Email support',
                ],
                'stripe_price_id' => 'price_basic',
                'paypal_plan_id' => 'P-BASIC',
            ],
            [
                'id' => 'pro',
                'name' => 'Professional',
                'description' => 'Professional plan for growing businesses',
                'price' => 19.99,
                'currency' => 'EUR',
                'features' => [
                    '20 projects',
                    '500 licences',
                    'Priority email support',
                    'API access',
                ],
                'stripe_price_id' => 'price_pro',
                'paypal_plan_id' => 'P-PRO',
            ],
            [
                'id' => 'enterprise',
                'name' => 'Enterprise',
                'description' => 'Enterprise plan for large businesses',
                'price' => 49.99,
                'currency' => 'EUR',
                'features' => [
                    'Unlimited projects',
                    'Unlimited licences',
                    'Priority support 24/7',
                    'API access',
                    'Custom branding',
                ],
                'stripe_price_id' => 'price_enterprise',
                'paypal_plan_id' => 'P-ENTERPRISE',
            ],
        ];
        
        return view('subscription.plans', compact('tenant', 'subscription', 'plans'));
    }
    
    /**
     * Display the checkout page for a specific plan.
     *
     * @param  string  $planId
     * @return \Illuminate\View\View
     */
    public function checkout($planId)
    {
        // Get the current tenant
        $tenant = Auth::user()->tenant;
        
        // Get the selected plan
        $plans = config('subscription.plans');
        $plan = collect($plans)->firstWhere('id', $planId);
        
        if (!$plan) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Selected plan not found.');
        }
        
        // Get payment methods
        $paymentMethods = $tenant->paymentMethods;
        
        return view('subscription.checkout', compact('tenant', 'plan', 'paymentMethods'));
    }
    
    /**
     * Process a Stripe subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processStripeSubscription(Request $request)
    {
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
     * Display the subscription success page.
     *
     * @return \Illuminate\View\View
     */
    public function success()
    {
        // Get the current tenant
        $tenant = Auth::user()->tenant;
        
        // Get the current subscription
        $subscription = $tenant->subscriptions()->first();
        
        return view('subscription.success', compact('tenant', 'subscription'));
    }
    
    /**
     * Display the payment methods page.
     *
     * @return \Illuminate\View\View
     */
    public function paymentMethods()
    {
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
            
            return redirect()->route('subscription.plans')
                ->with('success', 'Subscription cancelled successfully.');
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
            
            return redirect()->route('subscription.plans')
                ->with('success', 'Subscription resumed successfully.');
        } catch (\Exception $e) {
            Log::error('Resume subscription error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}