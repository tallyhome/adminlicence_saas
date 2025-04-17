<?php

use App\Http\Controllers\Admin\ApiKeyController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailProviderController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\Mail\Providers\PHPMailController;
use App\Http\Controllers\Admin\Mail\Providers\MailchimpController;
use App\Http\Controllers\Admin\Mail\Providers\RapidmailController;
use App\Http\Controllers\Admin\SerialKeyController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TwoFactorAuthController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\VersionController;
use App\Http\Controllers\Admin\ApiDocumentationController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\ClientExampleController;
use App\Http\Controllers\Admin\EmailVariableController;
use Illuminate\Support\Facades\Route;

// Routes d'authentification
Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login']);
    
    // Routes de réinitialisation de mot de passe
    Route::get('password/reset', [AdminAuthController::class, 'showLinkRequestForm'])->name('admin.password.request');
    Route::post('password/email', [AdminAuthController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('password/reset/{token}', [AdminAuthController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('password/reset', [AdminAuthController::class, 'reset'])->name('admin.password.update');
    
    // Routes pour l'authentification à deux facteurs
    Route::get('2fa/verify', [AdminAuthController::class, 'showTwoFactorForm'])->name('admin.2fa.verify');
    Route::post('2fa/verify', [AdminAuthController::class, 'verifyTwoFactor']);
    Route::get('2fa/recovery', function () { return view('auth.admin-2fa-recovery'); })->name('admin.2fa.recovery');
    Route::post('2fa/recovery', [TwoFactorAuthController::class, 'useRecoveryCode']);
});

// Routes protégées
Route::middleware('auth:admin')->group(function () {
    // Déconnexion
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Tableau de bord
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Informations de version
    Route::get('/version', [VersionController::class, 'index'])->name('admin.version');

    // Documentation
    Route::get('/api-documentation', [ApiDocumentationController::class, 'index'])->name('admin.api.documentation');
    
    Route::get('/licence-documentation', [ApiDocumentationController::class, 'licenceDocumentation'])->name('admin.licence.documentation');
    
    Route::get('/email-documentation', [ApiDocumentationController::class, 'emailDocumentation'])->name('admin.email.documentation');
    
    Route::get('/saas-documentation', [ApiDocumentationController::class, 'saasDocumentation'])->name('admin.saas.documentation');

    // Exemples d'intégration client
    Route::get('/client-example', [ClientExampleController::class, 'index'])->name('admin.client-example');

    // Gestion des projets
    Route::resource('projects', ProjectController::class)
        ->names([
            'index' => 'admin.projects.index',
            'create' => 'admin.projects.create',
            'store' => 'admin.projects.store',
            'show' => 'admin.projects.show',
            'edit' => 'admin.projects.edit',
            'update' => 'admin.projects.update',
            'destroy' => 'admin.projects.destroy'
        ]);
    Route::get('projects/{project}/serial-keys', [ProjectController::class, 'serialKeys'])->name('admin.projects.serial-keys');
    Route::get('projects/{project}/api-keys', [ProjectController::class, 'apiKeys'])->name('admin.projects.api-keys');

    // Gestion des clés de licence
    Route::resource('serial-keys', SerialKeyController::class)
        ->names([
            'index' => 'admin.serial-keys.index',
            'create' => 'admin.serial-keys.create',
            'store' => 'admin.serial-keys.store',
            'show' => 'admin.serial-keys.show',
            'edit' => 'admin.serial-keys.edit',
            'update' => 'admin.serial-keys.update',
            'destroy' => 'admin.serial-keys.destroy'
        ]);
    Route::patch('serial-keys/{serialKey}/revoke', [SerialKeyController::class, 'revoke'])->name('admin.serial-keys.revoke');
    Route::patch('serial-keys/{serialKey}/suspend', [SerialKeyController::class, 'suspend'])->name('admin.serial-keys.suspend');
    Route::patch('serial-keys/{serialKey}/reactivate', [SerialKeyController::class, 'reactivate'])->name('admin.serial-keys.reactivate');

    // Gestion des clés API
    Route::resource('api-keys', ApiKeyController::class)
        ->names([
            'index' => 'admin.api-keys.index',
            'create' => 'admin.api-keys.create',
            'store' => 'admin.api-keys.store',
            'show' => 'admin.api-keys.show',
            'edit' => 'admin.api-keys.edit',
            'update' => 'admin.api-keys.update',
            'destroy' => 'admin.api-keys.destroy'
        ]);
    Route::patch('api-keys/{apiKey}/revoke', [ApiKeyController::class, 'revoke'])->name('admin.api-keys.revoke');
    Route::patch('api-keys/{apiKey}/reactivate', [ApiKeyController::class, 'reactivate'])->name('admin.api-keys.reactivate');
    Route::patch('api-keys/{apiKey}/permissions', [ApiKeyController::class, 'updatePermissions'])->name('admin.api-keys.update-permissions');

    // Configuration des emails
    Route::prefix('mail')->name('admin.mail.')->group(function () {
        Route::get('settings', [MailController::class, 'index'])->name('settings');
        Route::post('settings', [MailController::class, 'store'])->name('settings.store');

        // Gestion des fournisseurs d'email
        Route::prefix('providers')->name('providers.')->group(function () {
            Route::get('/', [EmailProviderController::class, 'index'])->name('index');
            Route::put('/', [EmailProviderController::class, 'updateProvider'])->name('update');
            Route::post('/test', [EmailProviderController::class, 'testProvider'])->name('test');

            // PHPMail
            Route::prefix('phpmail')->name('phpmail.')->group(function () {
                Route::get('/', [PHPMailController::class, 'index'])->name('index');
                Route::put('/', [PHPMailController::class, 'update'])->name('update');
                Route::post('/test', [PHPMailController::class, 'test'])->name('test');
                Route::get('/logs', [PHPMailController::class, 'logs'])->name('logs');
                Route::post('/logs/clear', [PHPMailController::class, 'clearLogs'])->name('logs.clear');
            });

            // Mailgun
            Route::prefix('mailgun')->name('mailgun.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\Mail\Providers\MailgunController::class, 'index'])->name('index');
                Route::put('/', [App\Http\Controllers\Admin\Mail\Providers\MailgunController::class, 'update'])->name('update');
                Route::post('/test', [App\Http\Controllers\Admin\Mail\Providers\MailgunController::class, 'test'])->name('test');
                Route::get('/logs', [App\Http\Controllers\Admin\Mail\Providers\MailgunController::class, 'logs'])->name('logs');
                Route::post('/logs/clear', [App\Http\Controllers\Admin\Mail\Providers\MailgunController::class, 'clearLogs'])->name('logs.clear');
            });

            // Mailchimp
            Route::prefix('mailchimp')->name('mailchimp.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\Mail\Providers\MailchimpController::class, 'index'])->name('index');
                Route::put('/', [App\Http\Controllers\Admin\Mail\Providers\MailchimpController::class, 'update'])->name('update');
                Route::post('/test', [App\Http\Controllers\Admin\Mail\Providers\MailchimpController::class, 'test'])->name('test');
                Route::post('/sync-lists', [App\Http\Controllers\Admin\Mail\Providers\MailchimpController::class, 'syncLists'])->name('sync-lists');
                Route::post('/sync-templates', [App\Http\Controllers\Admin\Mail\Providers\MailchimpController::class, 'syncTemplates'])->name('sync-templates');
                Route::get('/campaigns', [App\Http\Controllers\Admin\Mail\Providers\MailchimpController::class, 'campaigns'])->name('campaigns');
                Route::post('/campaigns', [App\Http\Controllers\Admin\Mail\Providers\MailchimpController::class, 'createCampaign'])->name('campaigns.create');
                Route::post('/campaigns/{campaign}/send', [App\Http\Controllers\Admin\Mail\Providers\MailchimpController::class, 'sendCampaign'])->name('campaigns.send');
            });

            // Rapidmail
            Route::prefix('rapidmail')->name('rapidmail.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\Mail\Providers\RapidmailController::class, 'index'])->name('index');
                Route::put('/', [App\Http\Controllers\Admin\Mail\Providers\RapidmailController::class, 'update'])->name('update');
                Route::post('/test', [App\Http\Controllers\Admin\Mail\Providers\RapidmailController::class, 'test'])->name('test');
                Route::get('/lists', [App\Http\Controllers\Admin\Mail\Providers\RapidmailController::class, 'recipientLists'])->name('lists');
                Route::post('/lists', [App\Http\Controllers\Admin\Mail\Providers\RapidmailController::class, 'createRecipientList'])->name('lists.create');
                Route::get('/mailings', [App\Http\Controllers\Admin\Mail\Providers\RapidmailController::class, 'mailings'])->name('mailings');
                Route::post('/mailings', [App\Http\Controllers\Admin\Mail\Providers\RapidmailController::class, 'createMailing'])->name('mailings.create');
                Route::post('/mailings/{mailing}/send', [App\Http\Controllers\Admin\Mail\Providers\RapidmailController::class, 'sendMailing'])->name('mailings.send');
                Route::get('/mailings/{mailing}/stats', [App\Http\Controllers\Admin\Mail\Providers\RapidmailController::class, 'statistics'])->name('mailings.stats');
            });
        });
    });

    // Gestion des templates d'email
    Route::prefix('email/templates')->name('admin.email.templates.')->group(function () {
        Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
        Route::get('/create', [EmailTemplateController::class, 'create'])->name('create');
        Route::post('/', [EmailTemplateController::class, 'store'])->name('store');
        Route::get('/{template}/edit', [EmailTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [EmailTemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [EmailTemplateController::class, 'destroy'])->name('destroy');
        Route::get('/{template}/preview', [EmailTemplateController::class, 'preview'])->name('preview');
    });

    // Routes pour la gestion des variables d'email
    Route::prefix('email/variables')->name('admin.email.variables.')->group(function () {
        Route::get('/', [EmailVariableController::class, 'index'])->name('index');
        Route::post('/', [EmailVariableController::class, 'store'])->name('store');
        Route::put('/{variable}', [EmailVariableController::class, 'update'])->name('update');
        Route::delete('/{variable}', [EmailVariableController::class, 'destroy'])->name('destroy');
    });

    // Route pour le changement de langue
    Route::post('set-language', [LanguageController::class, 'setLanguage'])->name('admin.set.language');

    // Routes pour les paramètres généraux
    Route::get('settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::put('settings/profile', [SettingsController::class, 'updateProfile'])->name('admin.settings.update-profile');
    Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('admin.settings.update-password');
    Route::put('settings/favicon', [SettingsController::class, 'updateFavicon'])->name('admin.settings.update-favicon');
    Route::put('settings/dark-mode', [SettingsController::class, 'toggleDarkMode'])->name('admin.settings.toggle-dark-mode');
    
    // Routes pour la configuration des paiements
    Route::get('settings/payment-integration', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'index'])->name('admin.settings.payment-integration');
    Route::post('settings/update-stripe', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'updateStripe'])->name('admin.settings.update-stripe');
    Route::post('settings/update-paypal', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'updatePayPal'])->name('admin.settings.update-paypal');
    
    // Routes pour l'authentification à deux facteurs
    Route::get('settings/two-factor', [TwoFactorAuthController::class, 'index'])->name('admin.settings.two-factor');
    Route::post('settings/two-factor/enable', [TwoFactorAuthController::class, 'enable'])->name('admin.settings.two-factor.enable');
    Route::post('settings/two-factor/disable', [TwoFactorAuthController::class, 'disable'])->name('admin.settings.two-factor.disable');
    Route::post('settings/two-factor/regenerate-recovery-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes'])->name('admin.settings.two-factor.regenerate-recovery-codes');
    Route::post('settings/verify-code', [TwoFactorAuthController::class, 'verifyCode'])->name('admin.settings.verify-code');
    Route::get('settings/test-google2fa', [TwoFactorController::class, 'testGoogle2FA'])->name('admin.settings.test-google2fa');
    
    // Documentation API - Route déjà définie à la ligne 54
    // Route supprimée pour éviter les conflits de redirection
    
    // Routes pour les tickets de support (admin standard)
    Route::prefix('tickets')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/create', [\App\Http\Controllers\Admin\SupportTicketController::class, 'create'])->name('admin.tickets.create');
        Route::post('/', [\App\Http\Controllers\Admin\SupportTicketController::class, 'store'])->name('admin.tickets.store');
        Route::get('/{ticket}', [\App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('admin.tickets.show');
        Route::patch('/{ticket}/status', [\App\Http\Controllers\Admin\SupportTicketController::class, 'updateStatus'])->name('admin.tickets.update-status');
        Route::post('/{ticket}/reply', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('admin.tickets.reply');
        Route::post('/{ticket}/forward-to-super-admin', [\App\Http\Controllers\Admin\SupportTicketController::class, 'forwardToSuperAdmin'])->name('admin.tickets.forward-to-super-admin');
    });
    
    // Routes pour les tickets superadmin
    Route::prefix('super/tickets')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'index'])->name('admin.super.tickets.index');
        Route::get('/{ticket}', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'show'])->name('admin.super.tickets.show');
        Route::post('/{ticket}/reply', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'reply'])->name('admin.super.tickets.reply');
        Route::post('/{ticket}/close', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'close'])->name('admin.super.tickets.close');
        Route::post('/{ticket}/assign-to-admin', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'assignToAdmin'])->name('admin.super.tickets.assign-to-admin');
    });
    
    // Gestion des utilisateurs (superadmin et admin)
    Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
    
    // Gestion des administrateurs
    Route::put('/admins/{admin}', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateAdmin'])->name('admin.admins.update');
    
    // Gestion des rôles et permissions
    Route::prefix('roles')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'show'])->name('admin.roles.show');
        Route::get('/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('admin.roles.destroy');
        Route::post('/assign-to-admin/{admin}', [\App\Http\Controllers\Admin\RoleController::class, 'assignRolesToAdmin'])->name('admin.roles.assign-to-admin');
        Route::post('/assign-to-user/{user}', [\App\Http\Controllers\Admin\RoleController::class, 'assignRolesToUser'])->name('admin.roles.assign-to-user');
    });
    
    // Gestion des plans d'abonnement
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlanController::class, 'index'])->name('admin.subscriptions.index');
        Route::get('/create', [\App\Http\Controllers\Admin\PlanController::class, 'create'])->name('admin.subscriptions.create');
        Route::post('/', [\App\Http\Controllers\Admin\PlanController::class, 'store'])->name('admin.subscriptions.store');
        Route::get('/default-plans', [\App\Http\Controllers\Admin\PlanController::class, 'createDefaultPlans'])->name('admin.subscriptions.create-default-plans');
        Route::get('/plans', [\App\Http\Controllers\Admin\PlanController::class, 'index'])->name('admin.subscriptions.plans');
        Route::get('/{plan}/edit', [\App\Http\Controllers\Admin\PlanController::class, 'edit'])->name('admin.subscriptions.edit');
        Route::put('/{plan}', [\App\Http\Controllers\Admin\PlanController::class, 'update'])->name('admin.subscriptions.update');
        Route::delete('/{plan}', [\App\Http\Controllers\Admin\PlanController::class, 'destroy'])->name('admin.subscriptions.destroy');
    });

    // Routes pour les notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('admin.notifications.create');
        Route::post('/', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('admin.notifications.store');
        Route::get('/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getUnread'])->name('admin.notifications.unread');
        Route::post('/mark-as-read/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifications.mark-as-read');
        Route::post('/mark-all-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-as-read');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
        Route::put('/preferences', [\App\Http\Controllers\Admin\NotificationController::class, 'updatePreferences'])->name('admin.notifications.update-preferences');
    });
    
    // Routes pour les factures
    Route::prefix('invoices')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('admin.invoices.index');
        Route::get('/{invoice}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('admin.invoices.show');
        Route::get('/{invoice}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('admin.invoices.download');
    });
    
    // Routes pour les rapports
    Route::prefix('reports')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/revenue', [\App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('admin.reports.revenue');
        Route::get('/licenses', [\App\Http\Controllers\Admin\ReportController::class, 'licenses'])->name('admin.reports.licenses');
        Route::get('/users', [\App\Http\Controllers\Admin\ReportController::class, 'users'])->name('admin.reports.users');
        Route::get('/support', [\App\Http\Controllers\Admin\ReportController::class, 'support'])->name('admin.reports.support');
        Route::get('/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('admin.reports.export');
    });
});