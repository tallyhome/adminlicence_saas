<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SerialKey;
use App\Services\LicenceService;
use Illuminate\Http\Request;

class SerialKeyController extends Controller
{
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
            'project_id' => 'required|exists:projects,id',
            'status' => 'required|in:active,revoked,expired,suspended',
            'domain' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'expires_at' => 'nullable|date',
        ]);

        $serialKey->update($validated);

        return redirect()->route('admin.serial-keys.index')
            ->with('success', 'Clé de série mise à jour avec succès.');
    }

    /**
     * Révoquer une clé de série.
     *
     * @param  \App\Models\SerialKey  $serialKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke(SerialKey $serialKey)
    {
        $serialKey->update(['status' => 'revoked']);

        return redirect()->back()
            ->with('success', 'Clé de série révoquée avec succès.');
    }

    /**
     * Supprimer une clé de série.
     *
     * @param  \App\Models\SerialKey  $serialKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SerialKey $serialKey)
    {
        $serialKey->delete();

        return redirect()->route('admin.serial-keys.index')
            ->with('success', 'Clé de série supprimée avec succès.');
    }
    
    /**
     * Révoquer une clé de série.
     *
     * @param  \App\Models\SerialKey  $serialKey
     * @param  \App\Services\LicenceService  $licenceService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke(SerialKey $serialKey, LicenceService $licenceService)
    {
        $licenceService->revokeKey($serialKey);

        return redirect()->route('admin.serial-keys.show', $serialKey)
            ->with('success', 'Clé de série révoquée avec succès.');
    }
}