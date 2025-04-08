<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    /**
     * Les rôles de l'utilisateur
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Vérifie si l'utilisateur a une permission spécifique
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('slug', $permission);
        })->exists();
    }

    /**
     * Attribue un ou plusieurs rôles à l'utilisateur
     */
    public function assignRole(...$roles): self
    {
        $roles = array_flatten($roles);
        $roles = Role::whereIn('slug', $roles)->get();

        if($roles->isEmpty()) {
            return $this;
        }

        $this->roles()->saveMany($roles);

        return $this;
    }

    /**
     * Retire un ou plusieurs rôles de l'utilisateur
     */
    public function removeRole(...$roles): self
    {
        $roles = array_flatten($roles);
        $roles = Role::whereIn('slug', $roles)->get();

        $this->roles()->detach($roles);

        return $this;
    }

    /**
     * Vérifie si l'utilisateur est un super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Vérifie si l'utilisateur est un admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Vérifie si l'utilisateur est un utilisateur standard
     */
    public function isUser(): bool
    {
        return $this->hasRole('user');
    }
}