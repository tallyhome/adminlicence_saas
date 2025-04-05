<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Permissions API
    |--------------------------------------------------------------------------
    |
    | Liste des permissions disponibles pour les clés API.
    |
    */

    'permissions' => [
        'licences:read' => 'Lecture des licences',
        'licences:write' => 'Écriture des licences',
        'licences:delete' => 'Suppression des licences',
        'projects:read' => 'Lecture des projets',
        'projects:write' => 'Écriture des projets',
        'projects:delete' => 'Suppression des projets',
        'users:read' => 'Lecture des utilisateurs',
        'users:write' => 'Écriture des utilisateurs',
        'users:delete' => 'Suppression des utilisateurs',
        'statistics:read' => 'Lecture des statistiques',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configuration du rate limiting pour l'API.
    |
    */

    'rate_limiting' => [
        'enabled' => true,
        'max_attempts' => 60,
        'decay_minutes' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Expiration
    |--------------------------------------------------------------------------
    |
    | Configuration de l'expiration des tokens API.
    |
    */

    'token_expiration' => [
        'enabled' => true,
        'default_expiration' => 30, // jours
    ],
]; 