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
                'version' => '1.1.5',
                'date' => '05/04/2025',
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