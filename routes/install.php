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
    // Routes d'installation normales
    Route::prefix('install')->group(function () {
        // Page d'accueil du wizard d'installation
        Route::get('/', [InstallController::class, 'index'])->name('install.welcome');
        
        // Étape 1: Configuration de la base de données
        Route::get('/database', [InstallController::class, 'database'])->name('install.database');
        Route::post('/database', [InstallController::class, 'processDatabaseConfig'])->name('install.database.process');
        
        // Étape 2: Configuration de la langue
        Route::get('/language', [InstallController::class, 'language'])->name('install.language');
        Route::post('/language', [InstallController::class, 'processLanguageConfig'])->name('install.language.process');
        
        // Étape 3: Configuration des emails
        Route::get('/mail', [InstallController::class, 'mail'])->name('install.mail');
        Route::post('/mail', [InstallController::class, 'processMailConfig'])->name('install.mail.process');
        
        // Étape 4: Création du compte administrateur
        Route::get('/admin', [InstallController::class, 'admin'])->name('install.admin');
        Route::post('/admin', [InstallController::class, 'processAdminConfig'])->name('install.admin.process');
        
        // Étape finale: Installation terminée
        Route::get('/complete', [InstallController::class, 'complete'])->name('install.complete');
    });

    // Routes d'installation pour cPanel (dans le dossier public)
    Route::prefix('public/install')->group(function () {
        // Page d'accueil du wizard d'installation
        Route::get('/', [InstallController::class, 'index'])->name('install.welcome.cpanel');
        
        // Étape 1: Configuration de la base de données
        Route::get('/database', [InstallController::class, 'database'])->name('install.database.cpanel');
        Route::post('/database', [InstallController::class, 'processDatabaseConfig'])->name('install.database.process.cpanel');
        
        // Étape 2: Configuration de la langue
        Route::get('/language', [InstallController::class, 'language'])->name('install.language.cpanel');
        Route::post('/language', [InstallController::class, 'processLanguageConfig'])->name('install.language.process.cpanel');
        
        // Étape 3: Configuration des emails
        Route::get('/mail', [InstallController::class, 'mail'])->name('install.mail.cpanel');
        Route::post('/mail', [InstallController::class, 'processMailConfig'])->name('install.mail.process.cpanel');
        
        // Étape 4: Création du compte administrateur
        Route::get('/admin', [InstallController::class, 'admin'])->name('install.admin.cpanel');
        Route::post('/admin', [InstallController::class, 'processAdminConfig'])->name('install.admin.process.cpanel');
        
        // Étape finale: Installation terminée
        Route::get('/complete', [InstallController::class, 'complete'])->name('install.complete.cpanel');
    });
});