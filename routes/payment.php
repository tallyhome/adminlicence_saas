<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
|
| Routes spécifiques pour les fonctionnalités de paiement.
| Ces routes sont séparées pour éviter les problèmes avec les routes préfixées.
|
*/

// Routes directes pour les paiements
Route::get('/payment/stripe/{planId}', [PaymentController::class, 'showStripeForm'])->name('payment.stripe.form');
Route::post('/payment/stripe/process', [PaymentController::class, 'processStripe'])->name('payment.stripe.process');
Route::get('/payment/paypal/{planId}', [PaymentController::class, 'showPaypalForm'])->name('payment.paypal.form');
Route::post('/payment/paypal/process', [PaymentController::class, 'processPaypal'])->name('payment.paypal.process');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success'); 