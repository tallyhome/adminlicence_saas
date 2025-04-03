<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MailConfigController;
use App\Http\Controllers\Admin\SerialKeyController;
use App\Http\Controllers\Admin\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    // Route du tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Routes de gestion des projets
    Route::resource('projects', ProjectController::class)->names([
        'index' => 'admin.projects.index',
        'create' => 'admin.projects.create',
        'store' => 'admin.projects.store',
        'show' => 'admin.projects.show',
        'edit' => 'admin.projects.edit',
        'update' => 'admin.projects.update',
        'destroy' => 'admin.projects.destroy',
    ]);

    // Routes de configuration des emails
    Route::get('/mail-settings', [MailConfigController::class, 'index'])->name('admin.mail.settings');
    Route::post('/mail-settings', [MailConfigController::class, 'store'])->name('admin.mail.settings.store');
    Route::post('/mail-test', [MailConfigController::class, 'test'])->name('admin.mail.test');

    // Routes de gestion des clés de série
    Route::resource('serial-keys', SerialKeyController::class)->names([
        'index' => 'admin.serial-keys.index',
        'create' => 'admin.serial-keys.create',
        'store' => 'admin.serial-keys.store',
        'show' => 'admin.serial-keys.show',
        'edit' => 'admin.serial-keys.edit',
        'update' => 'admin.serial-keys.update',
        'destroy' => 'admin.serial-keys.destroy',
    ]);
    
    // Routes additionnelles pour les clés de série
    Route::patch('/serial-keys/{serialKey}/revoke', [SerialKeyController::class, 'revoke'])->name('admin.serial-keys.revoke');
    Route::patch('/serial-keys/{serialKey}/suspend', [SerialKeyController::class, 'suspend'])->name('admin.serial-keys.suspend');

    // Documentation d'intégration client
    Route::get('/client-example', function () {
        return view('admin.client-example');
    })->name('admin.client-example');
});