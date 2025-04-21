<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;

class PaymentTestController extends Controller
{
    protected $subscriptionController;

    public function __construct(SubscriptionController $subscriptionController)
    {
        $this->subscriptionController = $subscriptionController;
    }

    /**
     * Affiche le tableau de bord de test des paiements.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return $this->subscriptionController->paymentTestDashboard();
    }

    /**
     * Teste un paiement Stripe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testStripePayment(Request $request)
    {
        return $this->subscriptionController->testStripePayment($request);
    }

    /**
     * Teste un paiement PayPal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testPayPalPayment(Request $request)
    {
        return $this->subscriptionController->testPayPalPayment($request);
    }
} 