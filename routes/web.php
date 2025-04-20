<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\VersionController;
use App\Http\Controllers\DirectNotificationController;
use App\Http\Controllers\DirectFixController;
use App\Http\Controllers\SolutionFinaleController;
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

// Routes d'authentification utilisateur
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Routes d'inscription utilisateur avec vérification d'email
Route::middleware('guest')->group(function () {
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

// Routes de vérification d'email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/welcome');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/welcome', function() {
    return view('auth.welcome');
})->middleware(['auth'])->name('welcome');

Route::get('/subscriptions/plans', function() {
    // Si l'utilisateur est connecté en tant qu'admin, rediriger vers la vue admin
    if (auth()->guard('admin')->check()) {
        return redirect('/admin/subscriptions/plans');
    }
    // Sinon, rediriger vers la vue publique
    return redirect('/subscription/plans');
})->name('subscriptions.plans');

// Redirection de la création de plan vers le dashboard admin
Route::get('/subscriptions/create', function() {
    // Rediriger vers la route de création de plan dans le dashboard admin
    return redirect('/admin/subscriptions/create');
})->name('subscriptions.create');

// Redirection de l'édition de plan vers le dashboard admin
Route::get('/subscriptions/{id}/edit', function($id) {
    // Vérifier si le plan existe
    $plan = \App\Models\Plan::find($id);
    
    if (!$plan) {
        // Si le plan n'existe pas, rediriger vers la liste des plans
        return redirect('/admin/subscriptions/plans')->with('error', 'Le plan demandé n\'existe pas.');
    }
    
    // Rediriger vers la route d'édition de plan dans le dashboard admin en utilisant une URL absolue
    return redirect("/admin/subscriptions/{$id}/edit");
})->name('subscriptions.edit');

// Redirection pour la création de plans par défaut
Route::get('/subscriptions/default-plans', function() {
    // Rediriger vers la route de création de plans par défaut dans le dashboard admin
    return redirect('/admin/subscriptions/default-plans');
})->name('subscriptions.default-plans');

// Route temporaire pour créer des plans par défaut (à utiliser uniquement en développement)
Route::get('/create-default-plans', function() {
    // Créer le plan Basic
    \App\Models\Plan::updateOrCreate(
        ['slug' => 'basic'],
        [
            'name' => 'Basique',
            'description' => 'Plan de base pour les petites équipes',
            'price' => 9.99,
            'billing_cycle' => 'monthly',
            'features' => ['5 projets', '10 licences', 'Support standard'],
            'is_active' => true,
            'stripe_price_id' => 'price_basic',
            'paypal_plan_id' => 'P-BASIC',
            'trial_days' => 14,
            'max_licenses' => 10,
            'max_projects' => 5,
            'max_clients' => 10
        ]
    );
    
    // Créer le plan Pro
    \App\Models\Plan::updateOrCreate(
        ['slug' => 'pro'],
        [
            'name' => 'Pro',
            'description' => 'Plan professionnel pour PME',
            'price' => 19.99,
            'billing_cycle' => 'monthly',
            'features' => ['20 projets', '50 licences', 'Support premium', 'API accès'],
            'is_active' => true,
            'stripe_price_id' => 'price_pro',
            'paypal_plan_id' => 'P-PRO',
            'trial_days' => 7,
            'max_licenses' => 50,
            'max_projects' => 20,
            'max_clients' => 50
        ]
    );
    
    // Créer le plan Enterprise
    \App\Models\Plan::updateOrCreate(
        ['slug' => 'enterprise'],
        [
            'name' => 'Enterprise',
            'description' => 'Plan entreprise pour grandes sociétés',
            'price' => 49.99,
            'billing_cycle' => 'monthly',
            'features' => ['Projets illimités', 'Licences illimitées', 'Support prioritaire 24/7', 'API accès', 'Personnalisation'],
            'is_active' => true,
            'stripe_price_id' => 'price_enterprise',
            'paypal_plan_id' => 'P-ENTERPRISE',
            'trial_days' => 0,
            'max_licenses' => 999,
            'max_projects' => 999,
            'max_clients' => 999
        ]
    );
    
    return redirect('/admin/subscriptions/plans')
        ->with('success', 'Plans par défaut créés avec succès.');
});

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Routes pour les conditions d'utilisation et politique de confidentialité
Route::get('/terms', function() {
    $page = \App\Models\LegalPage::getTerms();
    return view('auth.terms', compact('page'));
})->name('terms');

Route::get('/privacy', function() {
    $page = \App\Models\LegalPage::getPrivacy();
    return view('auth.privacy', compact('page'));
})->name('privacy');

// Route pour la page de bienvenue après inscription
Route::get('/welcome', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

// Webhook routes (no auth required)
Route::post('/webhooks/stripe', [WebhookController::class, 'handleStripeWebhook']);
Route::post('/webhooks/paypal', [WebhookController::class, 'handlePayPalWebhook']);

// Subscription plans - accessible sans authentification
Route::get('/subscription/plans', [App\Http\Controllers\SubscriptionController::class, 'plans'])->name('subscription.plans');

// Subscription routes (auth required)
Route::middleware(['auth'])->group(function () {
    // Redirection vers la page des plans
    Route::get('/subscriptions', function() {
        return redirect('/subscription/plans');
    })->name('subscriptions');
    
    // Autres routes d'abonnement qui nécessitent une authentification
    Route::get('/subscription/checkout/{planId}', [App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/subscription/process-stripe', [App\Http\Controllers\SubscriptionController::class, 'processStripeSubscription'])->name('subscription.process-stripe');
    Route::post('/subscription/process-paypal', [App\Http\Controllers\SubscriptionController::class, 'processPayPalSubscription'])->name('subscription.process-paypal');
    Route::get('/subscription/success', [App\Http\Controllers\SubscriptionController::class, 'success'])->name('subscription.success');
    
    // Payment methods
    Route::get('/subscription/payment-methods', [App\Http\Controllers\SubscriptionController::class, 'paymentMethods'])->name('subscription.payment-methods');
    Route::get('/subscription/add-payment-method/{type?}', [App\Http\Controllers\SubscriptionController::class, 'addPaymentMethod'])->name('subscription.add-payment-method');
    Route::post('/subscription/store-stripe-payment-method', [App\Http\Controllers\SubscriptionController::class, 'storeStripePaymentMethod'])->name('subscription.store-stripe-payment-method');
    Route::post('/subscription/store-paypal-payment-method', [App\Http\Controllers\SubscriptionController::class, 'storePayPalPaymentMethod'])->name('subscription.store-paypal-payment-method');
    Route::post('/subscription/set-default-payment-method/{id}', [App\Http\Controllers\SubscriptionController::class, 'setDefaultPaymentMethod'])->name('subscription.set-default-payment-method');
    Route::delete('/subscription/delete-payment-method/{id}', [App\Http\Controllers\SubscriptionController::class, 'deletePaymentMethod'])->name('subscription.delete-payment-method');
    
    // Invoices
    Route::get('/subscription/invoices', [App\Http\Controllers\SubscriptionController::class, 'invoices'])->name('subscription.invoices');
    Route::get('/subscription/invoices/{id}', [App\Http\Controllers\SubscriptionController::class, 'showInvoice'])->name('subscription.show-invoice');
    
    // Subscription management
    Route::post('/subscription/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::post('/subscription/resume', [App\Http\Controllers\SubscriptionController::class, 'resumeSubscription'])->name('subscription.resume');
});

// Routes de documentation
Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
Route::get('/documentation/api', [DocumentationController::class, 'apiIntegration'])->name('documentation.api');

// Redirection de /notifications vers /admin/notifications
Route::get('/notifications', function () {
    return redirect()->route('admin.notifications.index');
});

// Routes publiques pour les notifications (pour compatibilité avec le JavaScript)
Route::get('/api/notifications/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getUnreadPublic']);
Route::post('/api/notifications/mark-as-read/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsReadPublic']);
Route::post('/api/notifications/mark-all-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsReadPublic']);

// Routes directes pour les notifications sans middleware auth
Route::middleware('web')->group(function () {
    Route::post('/notifications/mark-as-read/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getUnread']);
    
    // Routes directes pour les notifications avec préfixe admin
    Route::post('/admin/notifications/mark-as-read/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead']);
    Route::post('/admin/notifications/mark-all-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead']);
    Route::get('/admin/notifications/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getUnread']);
});

// Route directe pour la mise à jour des notifications (solution radicale)
Route::post('/admin/direct-update-notification', [DirectNotificationController::class, 'update']);

// Route de correction directe pour les notifications (solution ultra-radicale)
Route::post('/fix-notification', [DirectFixController::class, 'fixNotification']);

// Routes pour la solution finale (liens directs)
Route::get('/solution-finale/marquer-comme-lu/{id}', [SolutionFinaleController::class, 'marquerCommeLu'])->name('solution-finale.marquer-comme-lu');
Route::get('/solution-finale/marquer-tout-comme-lu', [SolutionFinaleController::class, 'marquerToutCommeLu'])->name('solution-finale.marquer-tout-comme-lu');

// Inclure les routes admin
require __DIR__.'/admin.php';
