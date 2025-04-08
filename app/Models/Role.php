<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * Les utilisateurs qui ont ce rôle
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Les permissions associées à ce rôle
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Vérifie si le rôle a une permission spécifique
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Ajoute une ou plusieurs permissions au rôle
     */
    public function givePermissionTo(...$permissions): self
    {
        $permissions = collect($permissions)->flatten()->all();
        $permissions = $this->getPermissions($permissions);
        
        if($permissions === null) {
            return $this;
        }

        $this->permissions()->saveMany($permissions);

        return $this;
    }

    /**
     * Retire une ou plusieurs permissions du rôle
     */
    public function revokePermissionTo(...$permissions): self
    {
        $permissions = array_flatten($permissions);
        $permissions = $this->getPermissions($permissions);

        $this->permissions()->detach($permissions);

        return $this;
    }

    /**
     * Récupère les instances de Permission à partir des slugs
     */
    protected function getPermissions(array $permissions)
    {
        return Permission::whereIn('slug', $permissions)->get();
    }
}