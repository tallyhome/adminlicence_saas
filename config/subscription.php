<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | This file defines the subscription plans available in the application.
    | Each plan has a unique ID, name, description, price, features, and
    | payment gateway-specific IDs.
    |
    */
    
    'plans' => [
        [
            'id' => 'basic',
            'name' => 'Basic',
            'description' => 'Basic plan for small businesses',
            'price' => 9.99,
            'currency' => 'EUR',
            'features' => [
                '5 projects',
                '100 licences',
                'Email support',
            ],
            'stripe_price_id' => 'price_basic',
            'paypal_plan_id' => 'P-BASIC',
        ],
        [
            'id' => 'pro',
            'name' => 'Professional',
            'description' => 'Professional plan for growing businesses',
            'price' => 19.99,
            'currency' => 'EUR',
            'features' => [
                '20 projects',
                '500 licences',
                'Priority email support',
                'API access',
            ],
            'stripe_price_id' => 'price_pro',
            'paypal_plan_id' => 'P-PRO',
        ],
        [
            'id' => 'enterprise',
            'name' => 'Enterprise',
            'description' => 'Enterprise plan for large businesses',
            'price' => 49.99,
            'currency' => 'EUR',
            'features' => [
                'Unlimited projects',
                'Unlimited licences',
                'Priority support 24/7',
                'API access',
                'Custom branding',
            ],
            'stripe_price_id' => 'price_enterprise',
            'paypal_plan_id' => 'P-ENTERPRISE',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Trial Period
    |--------------------------------------------------------------------------
    |
    | The default trial period in days for new subscriptions.
    |
    */
    
    'trial_days' => 14,
    
    /*
    |--------------------------------------------------------------------------
    | Grace Period
    |--------------------------------------------------------------------------
    |
    | The number of days after a failed payment before the subscription is canceled.
    |
    */
    
    'grace_period_days' => 3,
];