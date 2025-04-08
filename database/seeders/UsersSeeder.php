<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création d'un super administrateur
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Administrateur',
                'password' => Hash::make('password'),
            ]
        );

        // Création d'un utilisateur simple
        $simpleUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Utilisateur Simple',
                'password' => Hash::make('password'),
            ]
        );

        // Attribution des rôles aux utilisateurs
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $userRole = Role::where('slug', 'user')->first();

        // Attribuer le rôle de super admin
        if ($superAdminRole && $superAdmin) {
            $superAdmin->roles()->sync([$superAdminRole->id]);
        }

        // Attribuer le rôle d'utilisateur simple
        if ($userRole && $simpleUser) {
            $simpleUser->roles()->sync([$userRole->id]);
        }
    }
}