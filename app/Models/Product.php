<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'admin_id',
        'user_id',
        'version',
        'is_active',
        'max_activations_per_licence',
        'licence_duration_days',
        'metadata',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'max_activations_per_licence' => 'integer',
        'licence_duration_days' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Relation avec l'administrateur propriétaire du produit
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    
    /**
     * Relation avec l'utilisateur propriétaire du produit
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les licences associées à ce produit
     */
    public function licences()
    {
        return $this->hasMany(Licence::class);
    }

    /**
     * Génère une nouvelle licence pour ce produit
     */
    public function generateLicence($userId, $expiresInDays = null)
    {
        $expiresAt = null;
        
        if ($expiresInDays !== null) {
            $expiresAt = now()->addDays($expiresInDays);
        } elseif ($this->licence_duration_days > 0) {
            $expiresAt = now()->addDays($this->licence_duration_days);
        }

        return Licence::create([
            'licence_key' => Licence::generateLicenceKey(),
            'user_id' => $userId,
            'product_id' => $this->id,
            'status' => Licence::STATUS_ACTIVE,
            'expires_at' => $expiresAt,
            'max_activations' => $this->max_activations_per_licence,
            'current_activations' => 0,
        ]);
    }
}
