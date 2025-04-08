<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Création des permissions
        $permissions = [
            // Permissions générales
            ['name' => 'Accéder au dashboard', 'slug' => 'access-dashboard'],
            ['name' => 'Gérer les paramètres', 'slug' => 'manage-settings'],
            
            // Permissions des licences
            ['name' => 'Voir les licences', 'slug' => 'view-licenses'],
            ['name' => 'Créer des licences', 'slug' => 'create-licenses'],
            ['name' => 'Éditer les licences', 'slug' => 'edit-licenses'],
            ['name' => 'Supprimer les licences', 'slug' => 'delete-licenses'],
            
            // Permissions des clients
            ['name' => 'Voir les clients', 'slug' => 'view-clients'],
            ['name' => 'Créer des clients', 'slug' => 'create-clients'],
            ['name' => 'Éditer les clients', 'slug' => 'edit-clients'],
            ['name' => 'Supprimer les clients', 'slug' => 'delete-clients'],
            
            // Permissions des projets
            ['name' => 'Voir les projets', 'slug' => 'view-projects'],
            ['name' => 'Créer des projets', 'slug' => 'create-projects'],
            ['name' => 'Éditer les projets', 'slug' => 'edit-projects'],
            ['name' => 'Supprimer les projets', 'slug' => 'delete-projects'],
            
            // Permissions des templates d'email
            ['name' => 'Voir les templates', 'slug' => 'view-templates'],
            ['name' => 'Créer des templates', 'slug' => 'create-templates'],
            ['name' => 'Éditer les templates', 'slug' => 'edit-templates'],
            ['name' => 'Supprimer les templates', 'slug' => 'delete-templates'],
            
            // Permissions des abonnements
            ['name' => 'Gérer les abonnements', 'slug' => 'manage-subscriptions'],
            ['name' => 'Voir les factures', 'slug' => 'view-invoices'],
            
            // Permissions des utilisateurs
            ['name' => 'Gérer les utilisateurs', 'slug' => 'manage-users'],
            ['name' => 'Gérer les rôles', 'slug' => 'manage-roles'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Création des rôles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Accès complet à toutes les fonctionnalités'
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Accès à la gestion des licences et des clients'
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Accès limité aux fonctionnalités de base'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }

        // Attribution des permissions aux rôles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();
        $user = Role::where('slug', 'user')->first();

        // Super Admin a toutes les permissions
        // Détacher d'abord toutes les permissions pour éviter les doublons
        $superAdmin->permissions()->detach();
        $superAdmin->givePermissionTo(Permission::all());

        // Admin a des permissions spécifiques
        // Détacher d'abord toutes les permissions pour éviter les doublons
        $admin->permissions()->detach();
        $admin->givePermissionTo([
            'access-dashboard',
            'view-licenses', 'create-licenses', 'edit-licenses',
            'view-clients', 'create-clients', 'edit-clients',
            'view-projects', 'create-projects', 'edit-projects',
            'view-templates', 'create-templates', 'edit-templates',
            'view-invoices'
        ]);

        // User a des permissions limitées
        // Détacher d'abord toutes les permissions pour éviter les doublons
        $user->permissions()->detach();
        $user->givePermissionTo([
            'access-dashboard',
            'view-licenses',
            'view-clients',
            'view-projects',
            'view-templates',
            'view-invoices'
        ]);
    }
}