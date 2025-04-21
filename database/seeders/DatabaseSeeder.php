<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Project;
use App\Models\SerialKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Exécuter le seeder des rôles et permissions en premier
        $this->call(RolesAndPermissionsSeeder::class);
        
        // Exécuter le seeder des utilisateurs
        $this->call(UsersSeeder::class);
        
        // Exécuter le seeder des plans
        $this->call(PlanSeeder::class);
        
        // Créer l'administrateur
        Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
            ]
        );

        // Créer des projets de test
        $projects = [
            [
                'name' => 'Projet Test 1',
                'description' => 'Description du projet test 1',
                'website_url' => 'https://projet1.example.com',
                'status' => 'active'
            ],
            [
                'name' => 'Projet Test 2',
                'description' => 'Description du projet test 2',
                'website_url' => 'https://projet2.example.com',
                'status' => 'active'
            ]
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            // Créer des clés de licence pour chaque projet
            for ($i = 0; $i < 5; $i++) {
                SerialKey::create([
                    'serial_key' => strtoupper(uniqid() . '-' . uniqid() . '-' . uniqid()),
                    'project_id' => $project->id,
                    'status' => 'active',
                    'expires_at' => now()->addYear()
                ]);
            }
        }

        $this->call([
            PlansTableSeeder::class,
        ]);
    }
}
