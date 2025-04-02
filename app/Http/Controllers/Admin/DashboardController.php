<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SerialKey;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord d'administration.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = [
            'total_projects' => Project::count(),
            'total_keys' => SerialKey::count(),
            'active_keys' => SerialKey::where('status', 'active')->count(),
            'revoked_keys' => SerialKey::where('status', 'revoked')->count(),
            'expired_keys' => SerialKey::where('status', 'expired')->count(),
            'suspended_keys' => SerialKey::where('status', 'suspended')->count(),
        ];

        $recentProjects = Project::latest()->take(5)->get();
        $recentKeys = SerialKey::with('project')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentProjects', 'recentKeys'));
    }
}