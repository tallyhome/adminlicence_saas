<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des passerelles de paiement
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient la configuration pour les différentes
    | passerelles de paiement supportées par l'application.
    |
    */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'enabled' => env('STRIPE_ENABLED', false) === 'true',
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'enabled' => env('PAYPAL_ENABLED', false) === 'true',
    ],
];
