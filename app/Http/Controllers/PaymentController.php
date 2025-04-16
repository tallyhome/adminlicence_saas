<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Services\PayPalService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $stripeService;
    protected $paypalService;

    public function __construct(StripeService $stripeService, PayPalService $paypalService)
    {
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
    }

    public function setupIntent()
    {
        // Seuls les admins/superadmins peuvent créer un setup intent
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        try {
            $intent = $this->stripeService->createSetupIntent();
            return response()->json(['clientSecret' => $intent->client_secret]);
        } catch (\Exception $e) {
            Log::error('Failed to create setup intent: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la configuration du paiement.'], 500);
        }
    }

    public function storePaymentMethod(Request $request)
    {
        // Seuls les admins/superadmins peuvent ajouter un moyen de paiement
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        $request->validate([
            'payment_method_id' => 'required|string',
            'is_default' => 'boolean'
        ]);

        try {
            $paymentMethod = $this->stripeService->attachPaymentMethod(
                $request->user()->tenant,
                $request->payment_method_id,
                $request->is_default ?? false
            );

            return response()->json($paymentMethod);
        } catch (\Exception $e) {
            Log::error('Failed to store payment method: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de l\'enregistrement du moyen de paiement.'], 500);
        }
    }

    public function deletePaymentMethod(Request $request, PaymentMethod $paymentMethod)
    {
        // Seuls les admins/superadmins peuvent supprimer un moyen de paiement
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        try {
            if ($request->user()->tenant_id !== $paymentMethod->tenant_id) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            if ($paymentMethod->provider === 'stripe') {
                $this->stripeService->detachPaymentMethod($paymentMethod->provider_id);
            }

            $paymentMethod->delete();

            return response()->json(['message' => 'Moyen de paiement supprimé avec succès']);
        } catch (\Exception $e) {
            Log::error('Failed to delete payment method: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la suppression du moyen de paiement.'], 500);
        }
    }

    public function createPayPalOrder(Request $request)
    {
        // Seuls les admins/superadmins peuvent créer une commande PayPal
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        try {
            $subscription = Subscription::findOrFail($request->subscription_id);
            $order = $this->paypalService->createOrder($subscription);
            return response()->json(['id' => $order->id]);
        } catch (\Exception $e) {
            Log::error('Failed to create PayPal order: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la création de la commande PayPal.'], 500);
        }
    }

    public function capturePayPalOrder(Request $request)
    {
        // Seuls les admins/superadmins peuvent capturer une commande PayPal
        if (!$this->isAdminOrSuperAdmin()) abort(403);

        try {
            $order = $this->paypalService->captureOrder($request->order_id);
            return response()->json($order);
        } catch (\Exception $e) {
            Log::error('Failed to capture PayPal order: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la capture du paiement PayPal.'], 500);
        }
    }
}