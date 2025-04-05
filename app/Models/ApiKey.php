<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ApiKey extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'key',
        'secret',
        'permissions',
        'last_used_at',
        'expires_at',
        'revoked_at',
        'usage_count',
    ];

    protected $casts = [
        'permissions' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * Relation avec le projet.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Vérifier si la clé est active.
     *
     * @return Attribute
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => !$this->revoked_at && (!$this->expires_at || $this->expires_at->isFuture())
        );
    }

    /**
     * Vérifier si la clé est expirée.
     *
     * @return Attribute
     */
    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->expires_at && $this->expires_at->isPast()
        );
    }

    /**
     * Vérifier si la clé est révoquée.
     *
     * @return Attribute
     */
    protected function isRevoked(): Attribute
    {
        return Attribute::make(
            get: fn () => (bool) $this->revoked_at
        );
    }

    /**
     * Obtenir le statut de la clé.
     *
     * @return Attribute
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_revoked) {
                    return 'revoked';
                }
                if ($this->is_expired) {
                    return 'expired';
                }
                return 'active';
            }
        );
    }
} 