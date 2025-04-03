<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SerialKeyController;
use App\Http\Controllers\Admin\AdminAuthController;
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

// Inclure les routes d'administration
require __DIR__.'/admin.php';

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Routes d'authentification admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Routes d'administration protégées
// Les routes d'administration sont définies dans admin.php
