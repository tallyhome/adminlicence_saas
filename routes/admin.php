<?php

use App\Http\Controllers\Admin\ApiKeyController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SerialKeyController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\VersionController;
use Illuminate\Support\Facades\Route;

// Routes d'authentification
Route::middleware('guest:admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login']);
});

// Routes protégées
Route::middleware('auth:admin')->group(function () {
    // Déconnexion
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Tableau de bord
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Informations de version
    Route::get('/version', [VersionController::class, 'index'])->name('admin.version');

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
    Route::get('mail/settings', [MailController::class, 'index'])->name('admin.mail.settings');
    Route::post('mail/settings', [MailController::class, 'store'])->name('admin.mail.settings.store');

    // Routes pour les paramètres généraux
    Route::get('settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('settings/profile', [SettingsController::class, 'updateProfile'])->name('admin.settings.update-profile');
    Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('admin.settings.update-password');
    Route::put('settings/favicon', [SettingsController::class, 'updateFavicon'])->name('admin.settings.update-favicon');
    Route::put('settings/dark-mode', [SettingsController::class, 'toggleDarkMode'])->name('admin.settings.toggle-dark-mode');
    
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
});