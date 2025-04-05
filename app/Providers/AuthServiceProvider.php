<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Commenté pour éviter les boucles de redirection
        // Register a fallback for the default 'login' route
        // if (!Route::has('login')) {
        //     Route::get('/login', function () {
        //         return redirect()->route('admin.login');
        //     })->name('login');
        // }
    }
}
