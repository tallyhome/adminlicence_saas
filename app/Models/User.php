<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'admin_id',
        'terms_accepted',
        'terms_accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Les rôles attribués à cet utilisateur
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
     * Vérifie si l'utilisateur a une permission spécifique via ses rôles
     */
    public function hasPermission(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Relation avec l'admin qui a créé cet utilisateur (multi-tenant)
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
    
    /**
     * Relation avec les tickets de support créés par l'utilisateur
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
    /**
     * Relation avec les factures de l'utilisateur
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    
    /**
     * Relation avec l'abonnement actif de l'utilisateur
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }
    
    /**
     * Relation avec les projets de l'utilisateur
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    
    /**
     * Relation avec les produits de l'utilisateur
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * Relation avec les licences de l'utilisateur
     */
    public function licences()
    {
        return $this->hasMany(Licence::class);
    }
}
