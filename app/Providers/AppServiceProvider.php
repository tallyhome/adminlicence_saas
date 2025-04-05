<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Définir la route 'login' au démarrage de l'application
        // Cette approche garantit que la route existe avant que tout composant essaie de l'utiliser
        Route::get('/login', function () {
            return redirect()->route('admin.login');
        })->name('login');
    }
}
