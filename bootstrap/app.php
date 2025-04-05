<?php

use App\Providers\FixLoginRouteServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withCommands([
        // Nous utilisons notre propre ServeCommand personnalisé
        // \Illuminate\Foundation\Console\ServeCommand::class,
        \Illuminate\Foundation\Console\OptimizeClearCommand::class,
        \Illuminate\Foundation\Console\OptimizeCommand::class,
        \Illuminate\Foundation\Console\ConfigCacheCommand::class,
        \Illuminate\Foundation\Console\ConfigClearCommand::class,
        \Illuminate\Foundation\Console\PackageDiscoverCommand::class,
        \Illuminate\Foundation\Console\CacheClearCommand::class,
        \Illuminate\Foundation\Console\CacheTableCommand::class,
        \Illuminate\Foundation\Console\VendorPublishCommand::class,
    ])
    ->withProviders([
        FixLoginRouteServiceProvider::class,
        \App\Providers\EventServiceProvider::class,
    ])
    ->create();
