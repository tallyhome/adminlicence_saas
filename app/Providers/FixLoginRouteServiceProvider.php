<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;

class FixLoginRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Remplacer la classe UrlGenerator par notre propre implémentation
        $this->app->singleton('url', function ($app) {
            $routes = $app['router']->getRoutes();
            
            $url = new class($routes, $app->make('request')) extends UrlGenerator {
                public function route($name, $parameters = [], $absolute = true)
                {
                    // Commenté pour éviter les boucles de redirection
                    // Si la route est 'login', utiliser 'admin.login' à la place
                    // if ($name === 'login') {
                    //     return parent::route('admin.login', $parameters, $absolute);
                    // }
                    
                    return parent::route($name, $parameters, $absolute);
                }
            };
            
            return $url;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
