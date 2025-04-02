<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SerialKey extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'serial_key',
        'status',
        'project_id',
        'domain',
        'ip_address',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the project that owns the serial key.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Generate a unique serial key.
     */
    public static function generateUniqueKey(): string
    {
        $key = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        
        // Ensure the key is unique
        while (self::where('serial_key', $key)->exists()) {
            $key = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        }
        
        return $key;
    }

    /**
     * Check if the serial key is valid.
     */
    public function isValid(): bool
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Check if the domain is authorized for this key.
     */
    public function isDomainAuthorized(string $domain): bool
    {
        return $this->domain === null || $this->domain === $domain;
    }

    /**
     * Check if the IP address is authorized for this key.
     */
    public function isIpAuthorized(string $ipAddress): bool
    {
        return $this->ip_address === null || $this->ip_address === $ipAddress;
    }
}