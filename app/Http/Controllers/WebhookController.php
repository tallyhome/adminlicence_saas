<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\User;
use App\Models\Admin;
use App\Events\NewPayment;
use App\Services\StripeService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
            
            // Créer une notification pour l'administrateur et l'utilisateur
            $this->createPaymentNotification($subscription, $invoice, true);
            
            // Mettre à jour le statut de l'abonnement
            $subscription->update([
                'status' => 'active',
                'current_period_end' => $provider === 'stripe' 
                    ? now()->addDays(30) // Par défaut 30 jours, à ajuster selon les données réelles
                    : now()->addDays(30),
                'auto_renew' => true
            ]);
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
                
                // Créer une notification pour l'activation de l'abonnement
                $this->createSubscriptionNotification($subscription, 'active');
            }
        }

        return response('Webhook traité', 200);
    }
    
    /**
     * Gérer un échec de paiement
     */
    protected function handleFailedPayment($paymentData, string $provider): Response
    {
        $subscriptionId = $provider === 'stripe' 
            ? $paymentData['subscription']
            : $paymentData['billing_agreement_id'];

        $subscription = Subscription::where($provider . '_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            // Créer une facture avec statut échoué
            $invoice = Invoice::create([
                'subscription_id' => $subscription->id,
                'tenant_id' => $subscription->tenant_id,
                'amount' => $provider === 'stripe' ? $paymentData['amount_due'] / 100 : $paymentData['amount']['total'],
                'payment_method' => $provider,
                'status' => 'failed',
                'provider_invoice_id' => $provider === 'stripe' ? $paymentData['id'] : $paymentData['id']
            ]);
            
            // Créer une notification pour l'échec de paiement
            $this->createPaymentNotification($subscription, $invoice, false);
        }

        return response('Webhook traité', 200);
    }
    
    /**
     * Créer une notification pour un paiement
     */
    protected function createPaymentNotification(Subscription $subscription, Invoice $invoice, bool $success): void
    {
        // Récupérer l'utilisateur et l'admin associés
        $user = User::where('tenant_id', $subscription->tenant_id)->first();
        $admin = $user ? Admin::find($user->admin_id) : null;
        $superAdmins = Admin::where('is_super_admin', true)->get();
        
        // Déterminer le message et le titre en fonction du succès ou de l'échec
        $title = $success ? 'Paiement réussi' : 'Paiement échoué';
        $message = $success 
            ? "Paiement de {$invoice->amount} € reçu pour l'abonnement #{$subscription->id}" 
            : "Le paiement de {$invoice->amount} € pour l'abonnement #{$subscription->id} a échoué";
        
        // Notification pour l'utilisateur
        if ($user) {
            Notification::create([
                'title' => $title,
                'message' => $message,
                'sender_id' => null,
                'sender_type' => Notification::SENDER_SYSTEM,
                'target_type' => Notification::TARGET_SPECIFIC,
                'target_ids' => [$user->id],
                'importance' => $success ? 'normal' : 'high',
            ]);
        }
        
        // Notification pour l'admin associé
        if ($admin) {
            Notification::create([
                'title' => $title,
                'message' => $message . ($user ? " (Utilisateur: {$user->name})" : ""),
                'sender_id' => null,
                'sender_type' => Notification::SENDER_SYSTEM,
                'target_type' => Notification::TARGET_SPECIFIC,
                'target_ids' => [$admin->id],
                'importance' => $success ? 'normal' : 'high',
            ]);
        }
        
        // Notification pour tous les superadmins en cas d'échec
        if (!$success && $superAdmins->count() > 0) {
            Notification::create([
                'title' => 'Alerte : ' . $title,
                'message' => $message . ($user ? " (Utilisateur: {$user->name})" : ""),
                'sender_id' => null,
                'sender_type' => Notification::SENDER_SYSTEM,
                'target_type' => Notification::TARGET_SPECIFIC,
                'target_ids' => $superAdmins->pluck('id')->toArray(),
                'importance' => 'high',
            ]);
        }
    }
    
    /**
     * Créer une notification pour un changement de statut d'abonnement
     */
    protected function createSubscriptionNotification(Subscription $subscription, string $status): void
    {
        // Récupérer l'utilisateur et l'admin associés
        $user = User::where('tenant_id', $subscription->tenant_id)->first();
        $admin = $user ? Admin::find($user->admin_id) : null;
        
        // Déterminer le message en fonction du statut
        $statusText = '';
        $importance = 'normal';
        
        switch ($status) {
            case 'active':
                $statusText = 'activé';
                break;
            case 'canceled':
                $statusText = 'annulé';
                $importance = 'high';
                break;
            case 'expired':
                $statusText = 'expiré';
                $importance = 'high';
                break;
            default:
                $statusText = $status;
        }
        
        $title = "Abonnement $statusText";
        $message = "Votre abonnement #{$subscription->id} a été $statusText.";
        
        // Notification pour l'utilisateur
        if ($user) {
            Notification::create([
                'title' => $title,
                'message' => $message,
                'sender_id' => null,
                'sender_type' => Notification::SENDER_SYSTEM,
                'target_type' => Notification::TARGET_SPECIFIC,
                'target_ids' => [$user->id],
                'importance' => $importance,
            ]);
        }
        
        // Notification pour l'admin associé
        if ($admin) {
            Notification::create([
                'title' => $title,
                'message' => $message . ($user ? " (Utilisateur: {$user->name})" : ""),
                'sender_id' => null,
                'sender_type' => Notification::SENDER_SYSTEM,
                'target_type' => Notification::TARGET_SPECIFIC,
                'target_ids' => [$admin->id],
                'importance' => $importance,
            ]);
        }
    }
}