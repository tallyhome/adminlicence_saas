<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprime les plans existants pour éviter les doublons
        Plan::truncate();
        
        // Crée les plans par défaut
        Plan::create([
            'name' => 'Basique',
            'slug' => 'basic',
            'description' => 'Plan de base pour les petites équipes',
            'price' => 9.99,
            'billing_cycle' => 'monthly',
            'features' => ['5 projets', '10 licences', 'Support standard'],
            'is_active' => true,
            'stripe_price_id' => 'price_basic',
            'paypal_plan_id' => 'P-BASIC',
            'trial_days' => 14,
            'max_licenses' => 10,
            'max_projects' => 5,
            'max_clients' => 10
        ]);
        
        Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'description' => 'Plan professionnel pour PME',
            'price' => 19.99,
            'billing_cycle' => 'monthly',
            'features' => ['20 projets', '50 licences', 'Support premium', 'API accès'],
            'is_active' => true,
            'stripe_price_id' => 'price_pro',
            'paypal_plan_id' => 'P-PRO',
            'trial_days' => 7,
            'max_licenses' => 50,
            'max_projects' => 20,
            'max_clients' => 50
        ]);
        
        Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'description' => 'Plan entreprise pour grandes sociétés',
            'price' => 49.99,
            'billing_cycle' => 'monthly',
            'features' => ['Projets illimités', 'Licences illimitées', 'Support prioritaire 24/7', 'API accès', 'Personnalisation'],
            'is_active' => true,
            'stripe_price_id' => 'price_enterprise',
            'paypal_plan_id' => 'P-ENTERPRISE',
            'trial_days' => 0,
            'max_licenses' => 999,
            'max_projects' => 999,
            'max_clients' => 999
        ]);
    }
}
