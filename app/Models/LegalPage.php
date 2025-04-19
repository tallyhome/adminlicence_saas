<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'title',
        'content',
        'last_updated_by',
    ];

    /**
     * Les types de pages légales
     */
    const TYPE_TERMS = 'terms';
    const TYPE_PRIVACY = 'privacy';
    
    /**
     * Récupère la page des conditions d'utilisation
     */
    public static function getTerms()
    {
        return self::firstOrCreate(
            ['type' => self::TYPE_TERMS],
            [
                'title' => 'Conditions Générales d\'Utilisation',
                'content' => '<h2>Conditions Générales d\'Utilisation</h2><p>Dernière mise à jour: ' . now()->format('d/m/Y') . '</p><p>Veuillez lire attentivement ces conditions générales d\'utilisation.</p>',
                'last_updated_by' => null,
            ]
        );
    }
    
    /**
     * Récupère la page de politique de confidentialité
     */
    public static function getPrivacy()
    {
        return self::firstOrCreate(
            ['type' => self::TYPE_PRIVACY],
            [
                'title' => 'Politique de Confidentialité',
                'content' => '<h2>Politique de Confidentialité</h2><p>Dernière mise à jour: ' . now()->format('d/m/Y') . '</p><p>Cette politique de confidentialité décrit comment nous collectons et utilisons vos données personnelles.</p>',
                'last_updated_by' => null,
            ]
        );
    }
    
    /**
     * Relation avec l'utilisateur qui a mis à jour la page
     */
    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'last_updated_by');
    }
}
