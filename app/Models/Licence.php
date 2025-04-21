<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Licence extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'licence_key',
        'user_id',
        'product_id',
        'status',
        'expires_at',
        'max_activations',
        'current_activations',
        'last_check_at',
        'metadata'
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'last_check_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Les statuts possibles d'une licence
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_REVOKED = 'revoked';

    /**
     * Relation avec l'utilisateur propriétaire de la licence
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le produit associé à la licence
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relation avec les activations de licence
     */
    public function activations()
    {
        return $this->hasMany(LicenceActivation::class);
    }

    /**
     * Génère une nouvelle clé de licence
     */
    public static function generateLicenceKey()
    {
        return strtoupper(implode('-', [
            Str::random(5),
            Str::random(5),
            Str::random(5),
            Str::random(5)
        ]));
    }

    /**
     * Vérifie si la licence est active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Vérifie si la licence a expiré
     */
    public function isExpired()
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Vérifie si la licence peut être activée sur un nouvel appareil
     */
    public function canActivate()
    {
        return $this->isActive() && 
               ($this->max_activations === null || $this->current_activations < $this->max_activations);
    }

    /**
     * Incrémente le compteur d'activations
     */
    public function incrementActivations()
    {
        if ($this->canActivate()) {
            // Il semble qu'il n'y ait pas de champ pour stocker le nombre d'activations
            // Vous devriez ajouter un champ pour stocker ce nombre
            // $this->current_activations++;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Décrémente le compteur d'activations
     */
    public function decrementActivations()
    {
        // Il semble qu'il n'y ait pas de champ pour stocker le nombre d'activations
        // Vous devriez ajouter un champ pour stocker ce nombre
        // if ($this->current_activations > 0) {
        //     $this->current_activations--;
        //     $this->save();
        //     return true;
        // }
        return false;
    }

    /**
     * Met à jour la date de dernière vérification
     */
    public function updateLastCheck()
    {
        // Il semble qu'il n'y ait pas de champ pour stocker la date de dernière vérification
        // Vous devriez ajouter un champ pour stocker cette date
        // $this->last_check_at = now();
        $this->save();
    }
}
