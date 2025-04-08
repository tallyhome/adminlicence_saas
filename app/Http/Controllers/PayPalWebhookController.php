<?php

namespace App\Http\Controllers;

use App\Events\NewPayment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Subscription;
use App\Services\PayPalService;
use App\Services\WebSocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    protected $paypalService;
    protected $webSocketService;

    public function __construct(PayPalService $paypalService, WebSocketService $webSocketService)
    {
        $this->paypalService = $paypalService;
        $this->webSocketService = $webSocketService;
    }

    /**
     * Handle PayPal webhook events.
     */
    public function handleWebhook(Request $request)
    {
        try {
            $event = $this->paypalService->verifyWebhookSignature($request->getContent(), $request->headers->all());
            
            $method = 'handle' . str_replace('.', '', ucwords($event['event_type'], '.'));
            
            if (method_exists($this, $method)) {
                return $this->{$method}($event['resource']);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('PayPal Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle subscription payment completed.
     */
    protected function handlePaymentSaleCompleted(array $resource)
    {
        $subscription = Subscription::where('paypal_subscription_id', $resource['billing_agreement_id'])->first();
        
        if ($subscription) {
            $invoice = Invoice::create([
                'subscription_id' => $subscription->id,
                'tenant_id' => $subscription->tenant_id,
                'provider' => Invoice::PROVIDER_PAYPAL,
                'provider_id' => $resource['id'],
                'total' => $resource['amount']['total'],
                'currency' => $resource['amount']['currency'],
                'status' => Invoice::STATUS_PAID,
                'payment_method_type' => 'paypal',
                'paid_at' => now()
            ]);

            // Mettre à jour le statut de l'abonnement
            $subscription->update([
                'status' => 'active',
                'ends_at' => null,
                'auto_renew' => true
            ]);

            // Créer les éléments de la facture
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => 'Abonnement ' . $subscription->plan->name,
                'amount' => $resource['amount']['total'],
                'currency' => $resource['amount']['currency']
            ]);

            // Émettre l'événement de nouveau paiement
            event(new NewPayment($invoice));

            // Notifier via WebSocket
            $this->webSocketService->notifyNewPayment($invoice);
            $this->webSocketService->notifySubscriptionUpdated($subscription);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle subscription cancelled.
     */
    protected function handleBillingSubscriptionCancelled(array $resource)
    {
        $subscription = Subscription::where('paypal_subscription_id', $resource['id'])->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'ends_at' => null,
                'canceled_at' => null,
                'auto_renew' => true
            ]);

            // Mettre à jour le statut du tenant
            $subscription->tenant->update([
                'subscription_status' => 'cancelled'
            ]);

            $this->webSocketService->notifySubscriptionUpdated($subscription);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle subscription suspended.
     */
    protected function handleBillingSubscriptionSuspended(array $resource)
    {
        $subscription = Subscription::where('paypal_subscription_id', $resource['id'])->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'suspended',
                'auto_renew' => false
            ]);

            // Mettre à jour le statut du tenant
            $subscription->tenant->update([
                'subscription_status' => 'suspended'
            ]);

            $this->webSocketService->notifySubscriptionUpdated($subscription);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle subscription activated.
     */
    protected function handleBillingSubscriptionActivated(array $resource)
    {
        $subscription = Subscription::where('paypal_subscription_id', $resource['id'])->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'ends_at' => null,
                'canceled_at' => null,
                'auto_renew' => true
            ]);

            // Mettre à jour le statut du tenant
            $subscription->tenant->update([
                'subscription_status' => 'active'
            ]);

            $this->webSocketService->notifySubscriptionUpdated($subscription);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle subscription updated.
     */
    protected function handleBillingSubscriptionUpdated(array $resource)
    {
        $subscription = Subscription::where('paypal_subscription_id', $resource['id'])->first();
        
        if ($subscription) {
            $status = $resource['status'];
            
            if ($status === 'ACTIVE' && $subscription->status !== 'active') {
                $subscription->update([
                    'status' => 'active',
                    'ends_at' => null,
                    'canceled_at' => null,
                    'auto_renew' => true
                ]);

                // Mettre à jour le statut du tenant
                $subscription->tenant->update([
                    'subscription_status' => 'active'
                ]);
            }

            $this->webSocketService->notifySubscriptionUpdated($subscription);
        }

        return response()->json(['status' => 'success']);
    }
}