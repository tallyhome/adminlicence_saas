<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Afficher la liste des projets.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $projects = Project::with(['serialKeys', 'apiKeys'])->paginate(10);
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Afficher le formulaire de création d'un projet.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Enregistrer un nouveau projet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create($validated);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Projet créé avec succès.');
    }

    /**
     * Afficher les détails d'un projet.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function show(Project $project)
    {
        $project->load(['serialKeys', 'apiKeys']);
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Afficher le formulaire d'édition d'un projet.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Mettre à jour un projet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Supprimer un projet.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projet supprimé avec succès.');
    }
}