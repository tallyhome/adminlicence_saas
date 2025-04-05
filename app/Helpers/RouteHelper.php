<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route as RouteFacade;

/**
 * Classe d'aide pour la gestion des routes
 */
class RouteHelper
{
    /**
     * Génère une URL pour une route nommée, avec gestion spéciale pour la route 'login'
     *
     * @param string $name Nom de la route
     * @param array $parameters Paramètres de la route
     * @param bool $absolute URL absolue ou relative
     * @return string
     */
    public static function route($name, $parameters = [], $absolute = true)
    {
        // Si la route est 'login', utiliser 'admin.login' à la place
        if ($name === 'login') {
            return route('admin.login', $parameters, $absolute);
        }
        
        // Sinon, utiliser la fonction route() standard
        return route($name, $parameters, $absolute);
    }
}
