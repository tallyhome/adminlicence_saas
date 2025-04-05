<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SerialKey;
use App\Models\Project;
use App\Models\LicenceHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord avec les statistiques.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de pagination
        $perPage = $request->input('per_page', 10);
        $validPerPage = in_array($perPage, [10, 25, 50, 100, 500, 1000]) ? $perPage : 10;

        // Statistiques générales
        $stats = [
            'total_keys' => SerialKey::count(),
            'active_keys' => SerialKey::where('status', 'active')->count(),
            'suspended_keys' => SerialKey::where('status', 'suspended')->count(),
            'revoked_keys' => SerialKey::where('status', 'revoked')->count(),
            'used_keys' => SerialKey::where('status', 'active')
                ->whereNotNull('domain')
                ->whereNotNull('ip_address')
                ->count(),
            'total_projects' => Project::count(),
        ];

        // Clés récentes avec pagination
        $recentKeys = SerialKey::with('project')
            ->orderBy('created_at', 'desc')
            ->paginate($validPerPage);

        // Historique des actions récentes
        $recentActions = LicenceHistory::with(['serialKey', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Statistiques d'utilisation par projet
        $projectStats = Project::withCount([
            'serialKeys', 
            'serialKeys as active_keys_count' => function ($query) {
                $query->where('status', 'active');
            },
            'serialKeys as used_keys_count' => function ($query) {
                $query->where('status', 'active')
                      ->where(function($q) {
                          $q->whereNotNull('domain')
                            ->orWhereNotNull('ip_address');
                      });
            },
            'serialKeys as available_keys_count' => function ($query) {
                $query->where('status', 'active')
                      ->whereNull('domain')
                      ->whereNull('ip_address');
            }
        ])->get();
        
        // Ajouter l'indicateur de faible disponibilité
        foreach ($projectStats as $project) {
            $project->is_running_low = ($project->serialKeys_count > 0) 
                ? ($project->available_keys_count / $project->serialKeys_count) < 0.1 
                : false;
        }

        // Statistiques d'utilisation par jour (30 derniers jours)
        $usageStats = LicenceHistory::where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentKeys',
            'recentActions',
            'projectStats',
            'usageStats',
            'validPerPage'
        ));
    }

    /**
     * Générer des statistiques pour l'API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiStats()
    {
        $stats = [
            'total_keys' => SerialKey::count(),
            'active_keys' => SerialKey::where('status', 'active')->count(),
            'suspended_keys' => SerialKey::where('status', 'suspended')->count(),
            'revoked_keys' => SerialKey::where('status', 'revoked')->count(),
            'total_projects' => Project::count(),
            'usage_last_30_days' => LicenceHistory::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
        ];

        return response()->json($stats);
    }
}