<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    /**
     * Afficher les informations de version de l'application.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $version = [
            'major' => config('version.major'),
            'minor' => config('version.minor'),
            'patch' => config('version.patch'),
            'release' => config('version.release'),
            'full' => config('version.full')(),
            'last_update' => config('version.last_update'),
        ];
        
        // Historique des versions
        $history = [
            [
                'version' => '1.8.0',
                'date' => '05/04/2025',
                'description' => 'Amélioration de l\'API et des points d\'accès pour la validation des licences',
                'changes' => [
                    'Création de points d\'entrée API directs pour la vérification des licences',
                    'Ajout d\'une route API de test pour vérifier que l\'API fonctionne correctement',
                    'Support des chemins d\'API avec et sans préfixe v1 pour la compatibilité',
                    'Amélioration de la gestion des erreurs dans l\'API',
                    'Ajout de logs pour le débogage des appels API',
                    'Mise à jour de la documentation pour l\'intégration de l\'API dans les applications clientes',
                    'Création d\'un outil de test pour vérifier le bon fonctionnement de l\'API',
                ],
            ],
            [
                'version' => '1.7.0',
                'date' => '05/04/2025',
                'description' => 'Amélioration majeure de la gestion des clés de licence',
                'changes' => [
                    'Système de recherche avancé pour les clés de licence (recherche par clé, domaine, IP, projet)',
                    'Filtres pour afficher les clés par projet, statut, domaine et adresse IP',
                    'Ajout du statut "Expirée" dans les filtres de clés de licence',
                    'Possibilité de générer jusqu\'a 100 000 clés de licence en une seule fois',
                    'Sélecteur de pagination amélioré avec options 10, 25, 50, 100, 500 et 1000 clés par page',
                    'Réorganisation de l\'interface de gestion des clés pour une meilleure ergonomie',
                    'Amélioration du calcul des "clés utilisées" sur le tableau de bord',
                    'Correction de l\'affichage des icônes de pagination qui débordaient de l\'écran',
                ],
            ],
            [
                'version' => '1.1.5',
                'date' => '01/04/2025',
                'description' => 'Amélioration de l\'interface utilisateur',
                'changes' => [
                    'Suppression des icônes de pagination',
                    'Optimisation de l\'affichage des listes',
                    'Correction de bugs mineurs',
                ],
            ],
            [
                'version' => '1.1.0',
                'date' => '15/05/2025',
                'description' => 'Ajout de nouvelles fonctionnalités',
                'changes' => [
                    'Système de notifications par email',
                    'Amélioration du tableau de bord',
                    'Ajout de statistiques avancées',
                ],
            ],
            [
                'version' => '1.0.0',
                'date' => '01/05/2025',
                'description' => 'Version initiale de l\'application',
                'changes' => [
                    'Mise en place du système de gestion de licences',
                    'Interface d\'administration',
                    'API pour la vérification des licences',
                ],
            ],
        ];
        
        return view('admin.version.index', compact('version', 'history'));
    }
}