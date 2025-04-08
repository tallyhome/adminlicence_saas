<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Invoice;
use App\Events\NewPayment;
use App\Services\StripeService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $stripeService;
    protected $paypalService;

    public function __construct(StripeService $stripeService, PayPalService $paypalService)
    {
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
    }

    /**
     * Gérer les webhooks Stripe
     */
    public function handleStripeWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        
        if (empty($sigHeader)) {
            return response()->json(['error' => 'En-tête de signature Stripe manquant'], 400);
        }
        
        try {
            $event = $this->stripeService->constructEvent($payload, $sigHeader);
            
            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    return $this->handleSuccessfulPayment($event->data->object, 'stripe');

                case 'customer.subscription.deleted':
                    return $this->handleSubscriptionCanceled($event->data->object, 'stripe');

                case 'customer.subscription.updated':
                    return $this->handleSubscriptionUpdated($event->data->object, 'stripe');

                default:
                    return response('Webhook traité', 200);
            }
        } catch (\Exception $e) {
            Log::error('Erreur webhook Stripe: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Gérer les webhooks PayPal
     */
    public function handlePayPalWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $headers = $request->headers->all();
        
        try {
            $event = $this->paypalService->verifyWebhookSignature($payload, $headers);
            
            switch ($event['event_type']) {
                case 'PAYMENT.SALE.COMPLETED':
                    return $this->handleSuccessfulPayment($event['resource'], 'paypal');

                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    return $this->handleSubscriptionCanceled($event['resource'], 'paypal');

                case 'BILLING.SUBSCRIPTION.UPDATED':
                    return $this->handleSubscriptionUpdated($event['resource'], 'paypal');

                default:
                    return response('Webhook traité', 200);
            }
        } catch (\Exception $e) {
            Log::error('Erreur webhook PayPal: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Gérer un paiement réussi
     */
    protected function handleSuccessfulPayment($paymentData, string $provider): Response
    {
        $subscriptionId = $provider === 'stripe' 
            ? $paymentData['subscription']
            : $paymentData['billing_agreement_id'];

        $subscription = Subscription::where($provider . '_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            // Créer une nouvelle facture
            $invoice = Invoice::create([
                'subscription_id' => $subscription->id,
                'tenant_id' => $subscription->tenant_id,
                'amount' => $provider === 'stripe' ? $paymentData['amount_paid'] / 100 : $paymentData['amount']['total'],
                'payment_method' => $provider,
                'status' => 'paid',
                'provider_invoice_id' => $provider === 'stripe' ? $paymentData['id'] : $paymentData['id']
            ]);

            // Émettre l'événement de nouveau paiement
            event(new NewPayment($invoice));
        }

        return response('Webhook traité', 200);
    }

    /**
     * Gérer l'annulation d'un abonnement
     */
    protected function handleSubscriptionCanceled($subscriptionData, string $provider): Response
    {
        $subscriptionId = $provider === 'stripe'
            ? $subscriptionData['id']
            : $subscriptionData['id'];

        $subscription = Subscription::where($provider . '_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'auto_renew' => false
            ]);
        }

        return response('Webhook traité', 200);
    }

    /**
     * Gérer la mise à jour d'un abonnement
     */
    protected function handleSubscriptionUpdated($subscriptionData, string $provider): Response
    {
        $subscriptionId = $provider === 'stripe'
            ? $subscriptionData['id']
            : $subscriptionData['id'];

        $subscription = Subscription::where($provider . '_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $status = $provider === 'stripe'
                ? $subscriptionData['status']
                : $subscriptionData['status'];

            if ($status === 'active' && $subscription->status !== 'active') {
                $subscription->update([
                    'status' => 'active',
                    'canceled_at' => null,
                    'auto_renew' => true
                ]);
            }
        }

        return response('Webhook traité', 200);
    }
}