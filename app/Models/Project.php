<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'website_url',
        'status',
        'user_id'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    /**
     * Get the serial keys associated with the project.
     */
    public function serialKeys(): HasMany
    {
        return $this->hasMany(SerialKey::class);
    }

    /**
     * Get the active serial keys count for this project.
     */
    public function activeKeysCount(): int
    {
        return $this->serialKeys()->where('status', 'active')->count();
    }
    
    /**
     * Get the used serial keys count for this project.
     * A key is considered used if it has a domain or IP address set.
     */
    public function usedKeysCount(): int
    {
        return $this->serialKeys()
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNotNull('domain')
                      ->orWhereNotNull('ip_address');
            })
            ->count();
    }
    
    /**
     * Get the available (unused) serial keys count for this project.
     */
    public function availableKeysCount(): int
    {
        return $this->serialKeys()
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('domain')
                      ->whereNull('ip_address');
            })
            ->count();
    }
    
    /**
     * Get the total number of keys for this project.
     */
    public function totalKeysCount(): int
    {
        return $this->serialKeys()->count();
    }
    
    /**
     * Check if the project is running low on available keys.
     * Returns true if less than 10% of keys are available.
     */
    public function isRunningLowOnKeys(): bool
    {
        $total = $this->totalKeysCount();
        if ($total === 0) {
            return false;
        }
        
        $available = $this->availableKeysCount();
        return ($available / $total) < 0.1;
    }

    /**
     * Relation avec les clés API
     */
    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    /**
     * Scope pour les projets actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Vérifie si le projet est actif
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
    
    /**
     * Relation avec l'utilisateur propriétaire du projet
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}