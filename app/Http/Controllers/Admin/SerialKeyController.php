<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SerialKey;
use App\Services\LicenceService;
use App\Services\LicenceHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SerialKeyController extends Controller
{
    protected $licenceService;
    protected $historyService;

    public function __construct(LicenceService $licenceService, LicenceHistoryService $historyService)
    {
        $this->licenceService = $licenceService;
        $this->historyService = $historyService;
    }

    /**
     * Afficher la liste des clés de série.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = SerialKey::with('project');

        // Filtrage par projet
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filtrage par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $serialKeys = $query->latest()->paginate(15);
        $projects = Project::all();

        return view('admin.serial-keys.index', compact('serialKeys', 'projects'));
    }

    /**
     * Afficher le formulaire de création d'une clé de série.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $projects = Project::all();
        return view('admin.serial-keys.create', compact('projects'));
    }

    /**
     * Enregistrer une nouvelle clé de série.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'domain' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'expires_at' => 'nullable|date',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $createdKeys = [];

        // Créer le nombre de clés demandé
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $serialKey = new SerialKey([
                'serial_key' => SerialKey::generateUniqueKey(),
                'project_id' => $validated['project_id'],
                'domain' => $validated['domain'] ?? null,
                'ip_address' => $validated['ip_address'] ?? null,
                'expires_at' => $validated['expires_at'] ?? null,
            ]);

            $serialKey->save();
            $createdKeys[] = $serialKey;

            // Enregistrer dans l'historique
            $this->historyService->logAction($serialKey, 'created', [
                'project_id' => $validated['project_id'],
                'domain' => $validated['domain'],
                'ip_address' => $validated['ip_address'],
                'expires_at' => $validated['expires_at']
            ]);
        }

        return redirect()->route('admin.serial-keys.index')
            ->with('success', count($createdKeys) . ' clé(s) de série créée(s) avec succès.');
    }

    /**
     * Afficher les détails d'une clé de série.
     *
     * @param  \App\Models\SerialKey  $serialKey
     * @return \Illuminate\View\View
     */
    public function show(SerialKey $serialKey)
    {
        return view('admin.serial-keys.show', compact('serialKey'));
    }

    /**
     * Afficher le formulaire d'édition d'une clé de série.
     *
     * @param  \App\Models\SerialKey  $serialKey
     * @return \Illuminate\View\View
     */
    public function edit(SerialKey $serialKey)
    {
        $projects = Project::all();
        return view('admin.serial-keys.edit', compact('serialKey', 'projects'));
    }

    /**
     * Mettre à jour une clé de série.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SerialKey  $serialKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SerialKey $serialKey)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,revoked,expired,suspended',
            'domain' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'expires_at' => 'nullable|date',
        ]);

        $oldData = $serialKey->toArray();
        $serialKey->update($validated);

        // Enregistrer dans l'historique
        $this->historyService->logAction($serialKey, 'updated', [
            'old_data' => $oldData,
            'new_data' => $validated
        ]);

        return redirect()->route('admin.serial-keys.show', $serialKey)
            ->with('success', 'Clé de série mise à jour avec succès.');
    }

    /**
     * Supprimer une clé de série.
     *
     * @param  \App\Models\SerialKey  $serialKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SerialKey $serialKey)
    {
        // Enregistrer dans l'historique avant la suppression
        $this->historyService->logAction($serialKey, 'deleted', [
            'project_id' => $serialKey->project_id,
            'serial_key' => $serialKey->serial_key
        ]);

        $serialKey->delete();

        return redirect()->route('admin.serial-keys.index')
            ->with('success', 'Clé de série supprimée avec succès.');
    }

    /**
     * Révoquer une clé de série.
     *
     * @param SerialKey $serialKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke(SerialKey $serialKey)
    {
        $this->licenceService->revokeKey($serialKey);
        
        // Enregistrer dans l'historique
        $this->historyService->logAction($serialKey, 'revoked', [
            'old_status' => $serialKey->getOriginal('status'),
            'new_status' => 'revoked',
            'performed_by' => Auth::id(),
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.serial-keys.show', $serialKey)
            ->with('success', 'La clé de série a été révoquée avec succès.');
    }

    /**
     * Suspendre une clé de série.
     *
     * @param SerialKey $serialKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspend(SerialKey $serialKey)
    {
        $this->licenceService->suspendKey($serialKey);
        
        // Enregistrer dans l'historique
        $this->historyService->logAction($serialKey, 'suspended', [
            'old_status' => $serialKey->getOriginal('status'),
            'new_status' => 'suspended',
            'performed_by' => Auth::id(),
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.serial-keys.show', $serialKey)
            ->with('success', 'La clé de série a été suspendue avec succès.');
    }
}