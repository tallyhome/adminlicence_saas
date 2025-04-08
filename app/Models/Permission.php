<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * Les rôles qui ont cette permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Vérifie si la permission est attribuée à un rôle spécifique
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Attribue la permission à un ou plusieurs rôles
     */
    public function assignRole(...$roles): self
    {
        $roles = collect($roles)->flatten()->all();
        $roles = $this->getRoles($roles);
        
        if($roles === null) {
            return $this;
        }

        $this->roles()->saveMany($roles);

        return $this;
    }

    /**
     * Retire la permission d'un ou plusieurs rôles
     */
    public function removeRole(...$roles): self
    {
        $roles = array_flatten($roles);
        $roles = $this->getRoles($roles);

        $this->roles()->detach($roles);

        return $this;
    }

    /**
     * Récupère les instances de Role à partir des slugs
     */
    protected function getRoles(array $roles)
    {
        return Role::whereIn('slug', $roles)->get();
    }
}