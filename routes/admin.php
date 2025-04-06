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
use Illuminate\Support\Facades\Route;

// Routes d'authentification
Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login']);
    
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

    // API Documentation Example
    Route::get('/api-documentation', [ApiDocumentationController::class, 'index'])->name('admin.client-example');

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

            // Mailchimp
            Route::prefix('mailchimp')->name('mailchimp.')->group(function () {
                Route::get('/', [Providers\MailchimpController::class, 'index'])->name('index');
                Route::put('/', [Providers\MailchimpController::class, 'update'])->name('update');
                Route::post('/test', [Providers\MailchimpController::class, 'test'])->name('test');
                Route::post('/sync-lists', [Providers\MailchimpController::class, 'syncLists'])->name('sync-lists');
                Route::post('/sync-templates', [Providers\MailchimpController::class, 'syncTemplates'])->name('sync-templates');
                Route::get('/campaigns', [Providers\MailchimpController::class, 'campaigns'])->name('campaigns');
                Route::post('/campaigns', [Providers\MailchimpController::class, 'createCampaign'])->name('campaigns.create');
                Route::post('/campaigns/{campaign}/send', [Providers\MailchimpController::class, 'sendCampaign'])->name('campaigns.send');
            });

            // Rapidmail
            Route::prefix('rapidmail')->name('rapidmail.')->group(function () {
                Route::get('/', [Providers\RapidmailController::class, 'index'])->name('index');
                Route::put('/', [Providers\RapidmailController::class, 'update'])->name('update');
                Route::post('/test', [Providers\RapidmailController::class, 'test'])->name('test');
                Route::get('/lists', [Providers\RapidmailController::class, 'recipientLists'])->name('lists');
                Route::post('/lists', [Providers\RapidmailController::class, 'createRecipientList'])->name('lists.create');
                Route::get('/mailings', [Providers\RapidmailController::class, 'mailings'])->name('mailings');
                Route::post('/mailings', [Providers\RapidmailController::class, 'createMailing'])->name('mailings.create');
                Route::post('/mailings/{mailing}/send', [Providers\RapidmailController::class, 'sendMailing'])->name('mailings.send');
                Route::get('/mailings/{mailing}/stats', [Providers\RapidmailController::class, 'statistics'])->name('mailings.stats');
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

    // Routes pour les paramètres généraux
    Route::get('settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('settings/profile', [SettingsController::class, 'updateProfile'])->name('admin.settings.update-profile');
    Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('admin.settings.update-password');
    Route::put('settings/favicon', [SettingsController::class, 'updateFavicon'])->name('admin.settings.update-favicon');
    Route::put('settings/dark-mode', [SettingsController::class, 'toggleDarkMode'])->name('admin.settings.toggle-dark-mode');
    
    // Routes pour l'authentification à deux facteurs
    Route::get('settings/two-factor', [TwoFactorAuthController::class, 'index'])->name('admin.settings.two-factor');
    Route::post('settings/two-factor/enable', [TwoFactorAuthController::class, 'enable'])->name('admin.settings.two-factor.enable');
    Route::post('settings/two-factor/disable', [TwoFactorAuthController::class, 'disable'])->name('admin.settings.two-factor.disable');
    Route::post('settings/two-factor/regenerate-recovery-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes'])->name('admin.settings.two-factor.regenerate-recovery-codes');
    Route::post('settings/verify-code', [TwoFactorAuthController::class, 'verifyCode'])->name('admin.settings.verify-code');
    Route::get('settings/test-google2fa', [TwoFactorController::class, 'testGoogle2FA'])->name('admin.settings.test-google2fa');
    
    // Documentation API
    Route::get('api/documentation', function () {
        return view('admin.api-documentation');
    })->name('admin.api.documentation');
    
    // Routes pour les tickets de support (admin standard)
    Route::prefix('tickets')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/{ticket}', [\App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('admin.tickets.show');
        Route::patch('/{ticket}/status', [\App\Http\Controllers\Admin\SupportTicketController::class, 'updateStatus'])->name('admin.tickets.update-status');
        Route::post('/{ticket}/reply', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('admin.tickets.reply');
        Route::post('/{ticket}/forward-to-super-admin', [\App\Http\Controllers\Admin\SupportTicketController::class, 'forwardToSuperAdmin'])->name('admin.tickets.forward-to-super-admin');
    });
    
    // Routes pour les tickets de support (super admin)
    Route::prefix('super/tickets')->middleware('auth.super_admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'index'])->name('admin.super.tickets.index');
        Route::get('/{ticket}', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'show'])->name('admin.super.tickets.show');
        Route::patch('/{ticket}/status', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'updateStatus'])->name('admin.super.tickets.update-status');
        Route::post('/{ticket}/reply', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'reply'])->name('admin.super.tickets.reply');
        Route::post('/{ticket}/return-to-admin', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'returnToAdmin'])->name('admin.super.tickets.return-to-admin');
        Route::post('/{ticket}/assign-to-admin', [\App\Http\Controllers\Admin\SuperAdminTicketController::class, 'assignToAdmin'])->name('admin.super.tickets.assign-to-admin');
    });
    
    // Routes pour les notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::get('/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('admin.notifications.unread');
        Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('admin.notifications.mark-as-read');
        Route::post('/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-as-read');
        Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
        Route::put('/preferences', [\App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('admin.notifications.update-preferences');
    });
});