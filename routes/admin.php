<?php

use App\Http\Controllers\Admin\ApiKeyController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SerialKeyController;
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
    
    // Documentation API
    Route::get('api/documentation', function () {
        return view('admin.api-documentation');
    })->name('admin.api.documentation');
});