<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Project;
use App\Services\ApiKeyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiKeyController extends Controller
{
    protected $apiKeyService;

    public function __construct(ApiKeyService $apiKeyService)
    {
        $this->apiKeyService = $apiKeyService;
    }

    /**
     * Afficher la liste des clés API.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = ApiKey::with('project');

        // Filtrage par projet
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filtrage par statut
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'active':
                    $query->whereNull('revoked_at')
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                        });
                    break;
                case 'revoked':
                    $query->whereNotNull('revoked_at');
                    break;
                case 'expired':
                    $query->whereNotNull('expires_at')
                        ->where('expires_at', '<=', now())
                        ->whereNull('revoked_at');
                    break;
                case 'used':
                    $query->whereNotNull('last_used_at');
                    break;
            }
        }

        $apiKeys = $query->latest()->paginate(25);
        $projects = Project::all();

        return view('admin.api-keys.index', compact('apiKeys', 'projects'));
    }

    /**
     * Afficher le formulaire de création d'une clé API.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $projects = Project::all();
        return view('admin.api-keys.create', compact('projects'));
    }

    /**
     * Enregistrer une nouvelle clé API.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $apiKey = $this->apiKeyService->generateKey(
            $project,
            $validated['name'],
            $validated['permissions'] ?? []
        );

        if ($validated['expires_at']) {
            $apiKey->expires_at = $validated['expires_at'];
            $apiKey->save();
        }

        return redirect()->route('admin.api-keys.show', $apiKey)
            ->with('success', __('Clé API créée avec succès. Conservez bien la clé et le secret, ils ne seront plus affichés.'));
    }

    /**
     * Afficher les détails d'une clé API.
     *
     * @param ApiKey $apiKey
     * @return \Illuminate\View\View
     */
    public function show(ApiKey $apiKey)
    {
        $stats = $this->apiKeyService->getUsageStats($apiKey);
        return view('admin.api-keys.show', compact('apiKey', 'stats'));
    }

    /**
     * Révocation d'une clé API.
     *
     * @param ApiKey $apiKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke(ApiKey $apiKey)
    {
        $this->apiKeyService->revokeKey($apiKey);
        return redirect()->route('admin.api-keys.show', $apiKey)
            ->with('success', __('Clé API révoquée avec succès.'));
    }

    /**
     * Réactivation d'une clé API.
     *
     * @param ApiKey $apiKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reactivate(ApiKey $apiKey)
    {
        $this->apiKeyService->reactivateKey($apiKey);
        return redirect()->route('admin.api-keys.show', $apiKey)
            ->with('success', __('Clé API réactivée avec succès.'));
    }

    /**
     * Mise à jour des permissions d'une clé API.
     *
     * @param Request $request
     * @param ApiKey $apiKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePermissions(Request $request, ApiKey $apiKey)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
        ]);

        $this->apiKeyService->updatePermissions($apiKey, $validated['permissions']);
        return redirect()->route('admin.api-keys.show', $apiKey)
            ->with('success', __('Permissions mises à jour avec succès.'));
    }

    /**
     * Suppression d'une clé API.
     *
     * @param ApiKey $apiKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();
        return redirect()->route('admin.api-keys.index')
            ->with('success', __('Clé API supprimée avec succès.'));
    }
}