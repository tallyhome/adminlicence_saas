<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LicenceApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\NotificationFixController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route de test pour vérifier que l'API fonctionne
Route::get('/test', [LicenceApiController::class, 'test']);

// Routes pour la validation des licences - Version directe (pour compatibilité)
Route::post('/check-serial', [LicenceApiController::class, 'checkSerial']);

// Routes pour la validation des licences - Version avec préfixe v1
Route::prefix('v1')->group(function () {
    // Route de test pour vérifier que l'API fonctionne
    Route::get('/test', [LicenceApiController::class, 'test']);
    
    // Route publique pour vérifier une clé de licence
    Route::post('/check-serial', [LicenceApiController::class, 'checkSerial']);
});

// Routes API pour les notifications
Route::prefix('notifications')->group(function () {
    // Route pour marquer une notification comme lue
    Route::post('/read/{id}', [NotificationApiController::class, 'markAsRead']);
    
    // Route pour marquer toutes les notifications comme lues
    Route::post('/read-all', [NotificationApiController::class, 'markAllAsRead']);
});

// Solution radicale pour les notifications - Routes sans middleware ni vérification
Route::post('/fix/notifications/read/{id}', [NotificationFixController::class, 'markAsRead']);
Route::post('/fix/notifications/read-all', [NotificationFixController::class, 'markAllAsRead']);