<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\SerialKey;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur administrateur
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password')
        ]);

        // Créer quelques projets de test
        $projects = [
            [
                'name' => 'Projet Test 1',
                'description' => 'Un projet de test pour démonstration',
            ],
            [
                'name' => 'Projet Test 2',
                'description' => 'Un autre projet de test',
            ]
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            // Créer quelques clés de licence pour chaque projet
            for ($i = 0; $i < 5; $i++) {
                SerialKey::create([
                    'project_id' => $project->id,
                    'serial_key' => strtoupper(bin2hex(random_bytes(16))),
                    'status' => $i < 3 ? 'active' : 'suspended',
                    'expires_at' => now()->addYear(),
                ]);
            }
        }
    }
}
