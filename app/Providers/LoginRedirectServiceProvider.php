<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\UrlGenerator;

class LoginRedirectServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Remplacer la classe UrlGenerator par notre propre implémentation
        $this->app->extend('url', function ($urlGenerator, $app) {
            $routes = $app['router']->getRoutes();
            
            // Créer une classe anonyme qui étend UrlGenerator
            return new class($routes, $app->make('request')) extends UrlGenerator {
                public function route($name, $parameters = [], $absolute = true)
                {
                    // Si la route est 'login', utiliser 'admin.login' à la place
                    if ($name === 'login') {
                        $adminLoginRoute = $this->routes->getByName('admin.login');
                        if ($adminLoginRoute) {
                            return parent::toRoute($adminLoginRoute, $parameters, $absolute);
                        }
                        return '/admin/login';
                    }
                    
                    return parent::route($name, $parameters, $absolute);
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Définir explicitement la route 'login' qui redirige vers 'admin.login'
        if (!Route::has('login')) {
            Route::get('/login', function () {
                return redirect()->to('/admin/login');
            })->name('login');
        }
    }
}