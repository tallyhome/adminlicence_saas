<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerialKeyHistory extends Model
{
    protected $table = 'serial_key_histories';

    protected $fillable = [
        'serial_key_id',
        'action',
        'details',
        'admin_id',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relation avec la clé de licence
     */
    public function serialKey(): BelongsTo
    {
        return $this->belongsTo(SerialKey::class);
    }

    /**
     * Relation avec l'administrateur
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
} 