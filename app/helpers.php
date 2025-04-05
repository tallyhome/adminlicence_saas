<?php

/**
 * Remplace la fonction route() de Laravel pour intercepter les appels à la route 'login'
 */
if (!function_exists('route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function route($name, $parameters = [], $absolute = true)
    {
        // Si la route est 'login', utiliser 'admin.login' à la place
        if ($name === 'login') {
            $name = 'admin.login';
        }
        
        return app('url')->route($name, $parameters, $absolute);
    }
}
