<?php

namespace App\Providers;

use App\Services\TranslationService;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TranslationService::class, function ($app) {
            return new TranslationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Rien à faire ici pour le moment
    }
}