<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Gratuit',
                'slug' => 'free',
                'description' => 'Plan gratuit avec fonctionnalités de base',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Gestion de 1 licence',
                    'Gestion de 1 projet',
                    'Gestion de 1 client',
                    'Support par email'
                ],
                'is_active' => true,
                'trial_days' => 0,
                'max_licenses' => 1,
                'max_projects' => 1,
                'max_clients' => 1
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'description' => 'Plan idéal pour les petites entreprises',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Gestion de 5 licences',
                    'Gestion de 10 projets',
                    'Gestion de 10 clients',
                    'Support prioritaire',
                    'Templates d\'email personnalisés',
                    'Rapports avancés'
                ],
                'is_active' => true,
                'trial_days' => 14,
                'max_licenses' => 5,
                'max_projects' => 10,
                'max_clients' => 10
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Plan complet pour les entreprises en croissance',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Gestion illimitée de licences',
                    'Gestion illimitée de projets',
                    'Gestion illimitée de clients',
                    'Support prioritaire 24/7',
                    'Templates d\'email personnalisés',
                    'Rapports avancés',
                    'API d\'intégration',
                    'Tableau de bord personnalisé',
                    'Gestion des rôles avancée'
                ],
                'is_active' => true,
                'trial_days' => 14,
                'max_licenses' => -1, // illimité
                'max_projects' => -1, // illimité
                'max_clients' => -1   // illimité
            ]
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}