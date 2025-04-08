<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Services\StripeService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    protected $stripeService;
    protected $paypalService;

    public function __construct(StripeService $stripeService, PayPalService $paypalService)
    {
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
    }

    /**
     * Afficher les moyens de paiement de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        $paymentMethods = $user->paymentMethods;

        return view('subscriptions.payment-methods', [
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Ajouter une nouvelle carte bancaire via Stripe.
     */
    public function addCard(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
            'is_default' => 'boolean'
        ]);

        try {
            $user = Auth::user();
            $paymentMethod = $this->stripeService->addPaymentMethod(
                $user,
                $request->payment_method_id,
                $request->is_default ?? false
            );

            return response()->json([
                'message' => 'Carte ajoutée avec succès',
                'payment_method' => $paymentMethod
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'ajout de la carte : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Définir un moyen de paiement par défaut.
     */
    public function setDefault(Request $request, PaymentMethod $paymentMethod)
    {
        try {
            $user = Auth::user();

            if ($paymentMethod->user_id !== $user->id) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            if ($paymentMethod->provider === 'stripe') {
                $this->stripeService->setDefaultPaymentMethod($user, $paymentMethod->provider_id);
            } else {
                $this->paypalService->setDefaultPaymentMethod($user, $paymentMethod->provider_id);
            }

            // Mettre à jour le statut par défaut dans la base de données
            PaymentMethod::where('user_id', $user->id)
                ->update(['is_default' => false]);
            
            $paymentMethod->is_default = true;
            $paymentMethod->save();

            return response()->json([
                'message' => 'Moyen de paiement défini par défaut avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la modification : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un moyen de paiement.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            $user = Auth::user();

            if ($paymentMethod->user_id !== $user->id) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            if ($paymentMethod->provider === 'stripe') {
                $this->stripeService->deletePaymentMethod($paymentMethod->provider_id);
            } else {
                $this->paypalService->deletePaymentMethod($paymentMethod->provider_id);
            }

            $paymentMethod->delete();

            return response()->json([
                'message' => 'Moyen de paiement supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }
}