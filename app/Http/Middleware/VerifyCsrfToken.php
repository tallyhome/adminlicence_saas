<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/admin/notifications/mark-as-read/*',
        '/admin/notifications/mark-all-as-read',
        '/notifications/mark-as-read/*',
        '/notifications/mark-all-as-read',
        '/api/notifications/*',
        '/api/fix/notifications/*'
    ];
}
