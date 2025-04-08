<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\StripeService;
use App\Services\WebSocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    protected $stripeService;
    protected $webSocketService;

    public function __construct(StripeService $stripeService, WebSocketService $webSocketService)
    {
        $this->stripeService = $stripeService;
        $this->webSocketService = $webSocketService;
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Invalid Stripe webhook payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        try {
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($event);

                case 'payment_intent.payment_failed':
                    return $this->handlePaymentIntentFailed($event);

                case 'customer.subscription.created':
                case 'customer.subscription.updated':
                    return $this->handleSubscriptionUpdated($event);

                case 'customer.subscription.deleted':
                    return $this->handleSubscriptionDeleted($event);

                case 'invoice.payment_succeeded':
                    return $this->handleInvoicePaymentSucceeded($event);

                case 'invoice.payment_failed':
                    return $this->handleInvoicePaymentFailed($event);

                default:
                    Log::info('Unhandled Stripe webhook event: ' . $event->type);
                    return response()->json(['message' => 'Unhandled event type']);
            }
        } catch (\Exception $e) {
            Log::error('Error handling Stripe webhook: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook handling failed'], 500);
        }
    }

    protected function handlePaymentIntentSucceeded(Event $event)
    {
        $paymentIntent = $event->data->object;
        $subscription = Subscription::where('stripe_subscription_id', $paymentIntent->metadata->subscription_id)->first();

        if ($subscription) {
            $subscription->update(['status' => 'active']);
            $this->webSocketService->notifySubscriptionUpdated($subscription);
        }

        return response()->json(['message' => 'Payment intent succeeded handled']);
    }

    protected function handlePaymentIntentFailed(Event $event)
    {
        $paymentIntent = $event->data->object;
        $subscription = Subscription::where('stripe_subscription_id', $paymentIntent->metadata->subscription_id)->first();

        if ($subscription) {
            $subscription->update(['status' => 'payment_failed']);
            $this->webSocketService->notifySubscriptionUpdated($subscription);
        }

        return response()->json(['message' => 'Payment intent failed handled']);
    }

    protected function handleSubscriptionUpdated(Event $event)
    {
        $stripeSubscription = $event->data->object;
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => $stripeSubscription->status,
                'current_period_end' => date('Y-m-d H:i:s', $stripeSubscription->current_period_end),
                'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end
            ]);

            $this->webSocketService->notifySubscriptionUpdated($subscription);
        }

        return response()->json(['message' => 'Subscription updated handled']);
    }

    protected function handleSubscriptionDeleted(Event $event)
    {
        $stripeSubscription = $event->data->object;
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
                'canceled_at' => now(),
                'auto_renew' => false
            ]);
            $this->webSocketService->notifySubscriptionUpdated($subscription);
        }

        return response()->json(['message' => 'Subscription deleted handled']);
    }

    protected function handleInvoicePaymentSucceeded(Event $event)
    {
        $stripeInvoice = $event->data->object;
        $tenant = Tenant::where('stripe_customer_id', $stripeInvoice->customer)->first();

        if ($tenant) {
            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'number' => $stripeInvoice->number,
                'total' => $stripeInvoice->total / 100,
                'status' => 'paid',
                'provider' => 'stripe',
                'provider_id' => $stripeInvoice->id,
                'currency' => strtoupper($stripeInvoice->currency),
                'paid_at' => now(),
                'billing_reason' => $stripeInvoice->billing_reason,
                'period_start' => date('Y-m-d H:i:s', $stripeInvoice->period_start),
                'period_end' => date('Y-m-d H:i:s', $stripeInvoice->period_end)
            ]);

            // Créer les éléments de la facture
            foreach ($stripeInvoice->lines->data as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item->description,
                    'amount' => $item->amount / 100,
                    'quantity' => $item->quantity,
                    'period_start' => date('Y-m-d H:i:s', $item->period->start),
                    'period_end' => date('Y-m-d H:i:s', $item->period->end)
                ]);
            }

            // Mettre à jour le statut de l'abonnement
            if ($stripeInvoice->subscription) {
                $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice->subscription)
                    ->first();
                if ($subscription) {
                    $subscription->update([
                        'status' => 'active',
                        'trial_ends_at' => null
                    ]);
                }
            }

            $this->webSocketService->notifyNewPayment($invoice);
            
            // Envoyer la notification de paiement réussi avec les détails de la facture
            $tenant->notify(new \App\Notifications\PaymentSucceeded($invoice));
        }

        return response()->json(['message' => 'Invoice payment succeeded handled']);
    }

    protected function handleInvoicePaymentFailed(Event $event)
    {
        $stripeInvoice = $event->data->object;
        $tenant = Tenant::where('stripe_customer_id', $stripeInvoice->customer)->first();

        if ($tenant) {
            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'number' => $stripeInvoice->number,
                'total' => $stripeInvoice->total / 100,
                'status' => 'failed',
                'provider' => 'stripe',
                'provider_id' => $stripeInvoice->id,
                'currency' => strtoupper($stripeInvoice->currency)
            ]);

            $this->webSocketService->notifyPaymentFailed($invoice);
            
            // Envoyer la notification d'échec de paiement
            $tenant->notify(new \App\Notifications\PaymentFailed($invoice));
        }

        return response()->json(['message' => 'Invoice payment failed handled']);
    }
}