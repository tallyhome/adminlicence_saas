<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenceHistory extends Model
{
    protected $fillable = [
        'serial_key_id',
        'action',
        'details',
        'performed_by',
        'ip_address'
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Obtenir la clé de série associée.
     */
    public function serialKey(): BelongsTo
    {
        return $this->belongsTo(SerialKey::class);
    }

    /**
     * Obtenir l'utilisateur qui a effectué l'action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}