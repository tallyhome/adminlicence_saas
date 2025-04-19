<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenceActivation extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'licence_id',
        'device_id',
        'device_name',
        'ip_address',
        'user_agent',
        'is_active',
        'activated_at',
        'deactivated_at',
        'metadata',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec la licence
     */
    public function licence()
    {
        return $this->belongsTo(Licence::class);
    }

    /**
     * Désactive cette activation
     */
    public function deactivate()
    {
        $this->is_active = false;
        $this->deactivated_at = now();
        $this->save();
        
        // Décrémenter le compteur d'activations sur la licence
        $this->licence->decrementActivations();
        
        return $this;
    }

    /**
     * Génère un identifiant unique pour un appareil
     */
    public static function generateDeviceId($userAgent, $ipAddress)
    {
        return md5($userAgent . $ipAddress . config('app.key'));
    }
}
