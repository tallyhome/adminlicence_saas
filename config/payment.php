<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des Passerelles de Paiement
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient les configurations pour les différentes passerelles
    | de paiement utilisées dans l'application.
    |
    */

    'stripe' => [
        'enabled' => env('STRIPE_ENABLED', true),
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'paypal' => [
        'enabled' => env('PAYPAL_ENABLED', true),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
        'sandbox' => env('PAYPAL_SANDBOX', true),
    ],
];
