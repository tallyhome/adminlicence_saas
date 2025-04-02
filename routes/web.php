<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SerialKeyController;
use App\Http\Controllers\Auth\AdminAuthController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Routes d'authentification admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Routes d'administration protégées
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestion des projets
    Route::resource('projects', ProjectController::class);
    
    // Gestion des clés de série
    Route::resource('serial-keys', SerialKeyController::class);
    Route::patch('/serial-keys/{serialKey}/revoke', [SerialKeyController::class, 'revoke'])->name('serial-keys.revoke');
    
    // Documentation d'intégration client
    Route::get('/client-example', function () {
        return view('admin.client-example');
    })->name('client-example');
});
