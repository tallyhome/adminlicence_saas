<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic-plan',
                'description' => 'Plan de base avec les fonctionnalités essentielles',
                'price' => 9.99,
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'features' => json_encode([
                    'max_licenses' => 5,
                    'support' => 'email',
                    'updates' => true
                ])
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro-plan',
                'description' => 'Plan professionnel avec plus de fonctionnalités',
                'price' => 19.99,
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'features' => json_encode([
                    'max_licenses' => 20,
                    'support' => '24/7',
                    'updates' => true,
                    'api_access' => true
                ])
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise-plan',
                'description' => 'Plan entreprise avec toutes les fonctionnalités',
                'price' => 49.99,
                'billing_cycle' => 'monthly',
                'trial_days' => 30,
                'features' => json_encode([
                    'max_licenses' => -1, // illimité
                    'support' => 'premium',
                    'updates' => true,
                    'api_access' => true,
                    'custom_features' => true
                ])
            ]
        ];

        foreach ($plans as $plan) {
            if (!Plan::where('slug', $plan['slug'])->exists()) {
                Plan::create($plan);
            }
        }
    }
} 