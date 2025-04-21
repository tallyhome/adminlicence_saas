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
        // Commenter temporairement Telescope pour résoudre des erreurs
        if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            // Ne pas enregistrer Telescope
            // $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            // $this->app->register(TelescopeServiceProvider::class);
        }

        // Enregistrer le service WebSocket en tant que singleton
        $this->app->singleton(\App\Services\WebSocketService::class, function ($app) {
            return new \App\Services\WebSocketService();
        });
        
        // Enregistrer le service Stripe avec WebSocketService
        $this->app->singleton(\App\Services\StripeService::class, function ($app) {
            return new \App\Services\StripeService(
                $app->make(\App\Services\WebSocketService::class)
            );
        });
        
        // Enregistrer le service PayPal
        $this->app->singleton(\App\Services\PayPalService::class, function ($app) {
            return new \App\Services\PayPalService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Commenté pour éviter les boucles de redirection
        // Définir la route 'login' au démarrage de l'application
        // Cette approche garantit que la route existe avant que tout composant essaie de l'utiliser
        // Route::get('/login', function () {
        //     return redirect()->route('admin.login');
        // })->name('login');
    }
}
