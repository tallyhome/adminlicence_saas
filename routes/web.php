<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\VersionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route principale qui redirige vers la page de connexion admin
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Route pour l'installation
Route::get('/install', function () {
    return redirect('/install.php');
})->name('install');

// Route publique pour la page de version
Route::get('/version', [VersionController::class, 'index'])->name('version');

// Define the login route that redirects to admin login
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Webhook routes (no auth required)
Route::post('/webhooks/stripe', [WebhookController::class, 'handleStripeWebhook']);
Route::post('/webhooks/paypal', [WebhookController::class, 'handlePayPalWebhook']);

// Subscription routes (auth required)
Route::middleware(['auth'])->group(function () {
    // Subscription plans
    Route::get('/subscription/plans', [\App\Http\Controllers\SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::get('/subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'plans'])->name('subscriptions'); // Redirection vers le nouveau dashboard
    Route::get('/subscription/checkout/{planId}', [\App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/subscription/process-stripe', [\App\Http\Controllers\SubscriptionController::class, 'processStripeSubscription'])->name('subscription.process-stripe');
    Route::post('/subscription/process-paypal', [\App\Http\Controllers\SubscriptionController::class, 'processPayPalSubscription'])->name('subscription.process-paypal');
    Route::get('/subscription/success', [\App\Http\Controllers\SubscriptionController::class, 'success'])->name('subscription.success');
    
    // Payment methods
    Route::get('/subscription/payment-methods', [SubscriptionController::class, 'paymentMethods'])->name('subscription.payment-methods');
    Route::get('/subscription/add-payment-method/{type?}', [SubscriptionController::class, 'addPaymentMethod'])->name('subscription.add-payment-method');
    Route::post('/subscription/store-stripe-payment-method', [SubscriptionController::class, 'storeStripePaymentMethod'])->name('subscription.store-stripe-payment-method');
    Route::post('/subscription/store-paypal-payment-method', [SubscriptionController::class, 'storePayPalPaymentMethod'])->name('subscription.store-paypal-payment-method');
    Route::post('/subscription/set-default-payment-method/{id}', [SubscriptionController::class, 'setDefaultPaymentMethod'])->name('subscription.set-default-payment-method');
    Route::delete('/subscription/delete-payment-method/{id}', [SubscriptionController::class, 'deletePaymentMethod'])->name('subscription.delete-payment-method');
    
    // Invoices
    Route::get('/subscription/invoices', [SubscriptionController::class, 'invoices'])->name('subscription.invoices');
    Route::get('/subscription/invoices/{id}', [SubscriptionController::class, 'showInvoice'])->name('subscription.show-invoice');
    
    // Subscription management
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::post('/subscription/resume', [SubscriptionController::class, 'resumeSubscription'])->name('subscription.resume');
});

// Routes de documentation
Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
Route::get('/documentation/api', [DocumentationController::class, 'apiIntegration'])->name('documentation.api');

// Inclure les routes admin
require __DIR__.'/admin.php';
