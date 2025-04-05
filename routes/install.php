<?php

use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Installation Routes
|--------------------------------------------------------------------------
|
| Ces routes sont utilisées pour le processus d'installation (wizard)
| de l'application. Elles ne sont accessibles que si l'application
| n'est pas encore installée.
|
*/

Route::middleware(['web'])->group(function () {
    // Page d'accueil du wizard d'installation
    Route::get('/install', [InstallController::class, 'index'])->name('install.index');
    
    // Étape 1: Configuration de la base de données
    Route::get('/install/database', [InstallController::class, 'database'])->name('install.database');
    Route::post('/install/database', [InstallController::class, 'processDatabaseConfig'])->name('install.database.process');
    
    // Étape 2: Configuration de la langue
    Route::get('/install/language', [InstallController::class, 'language'])->name('install.language');
    Route::post('/install/language', [InstallController::class, 'processLanguageConfig'])->name('install.language.process');
    
    // Étape 3: Configuration des emails
    Route::get('/install/mail', [InstallController::class, 'mail'])->name('install.mail');
    Route::post('/install/mail', [InstallController::class, 'processMailConfig'])->name('install.mail.process');
    
    // Étape 4: Création du compte administrateur
    Route::get('/install/admin', [InstallController::class, 'admin'])->name('install.admin');
    Route::post('/install/admin', [InstallController::class, 'processAdminConfig'])->name('install.admin.process');
    
    // Étape finale: Installation terminée
    Route::get('/install/complete', [InstallController::class, 'complete'])->name('install.complete');
});