<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ProjectController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche la liste des projets de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        $projects = $user->projects()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('user.projects.index', compact('projects'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau projet.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Vérification des limites d'abonnement
        $subscription = $user->subscription;
        if ($subscription) {
            $plan = \App\Models\Plan::find($subscription->plan_id);
            if ($plan && $plan->max_projects > 0) {
                $currentCount = $user->projects()->count();
                if ($currentCount >= $plan->max_projects) {
                    return redirect()->route('user.dashboard')
                        ->with('error', "Vous avez atteint la limite de {$plan->max_projects} projets pour votre abonnement actuel. Veuillez mettre à niveau votre abonnement pour en créer davantage.");
                }
            }
        }
        
        return view('user.projects.create');
    }

    /**
     * Enregistre un nouveau projet.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url|max:255',
        ]);

        $user = Auth::user();
        
        // Vérification des limites d'abonnement
        $subscription = $user->subscription;
        if ($subscription) {
            $plan = \App\Models\Plan::find($subscription->plan_id);
            if ($plan && $plan->max_projects > 0) {
                $currentCount = $user->projects()->count();
                if ($currentCount >= $plan->max_projects) {
                    return redirect()->route('user.dashboard')
                        ->with('error', "Vous avez atteint la limite de {$plan->max_projects} projets pour votre abonnement actuel. Veuillez mettre à niveau votre abonnement pour en créer davantage.");
                }
            }
        }
        
        try {
            $project = new Project();
            $project->name = $request->name;
            $project->description = $request->description;
            $project->website_url = $request->website_url;
            $project->is_active = true;
            $project->user_id = $user->id;
            $project->save();
            
            Log::info('Projet créé avec succès', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'project_name' => $project->name
            ]);
            
            return redirect()->route('user.projects.show', $project->id)
                ->with('success', 'Projet créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du projet', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création du projet.');
        }
    }

    /**
     * Affiche les détails d'un projet.
     */
    public function show($id)
    {
        $user = Auth::user();
        $project = $user->projects()->findOrFail($id);
        
        // Récupérer les statistiques du projet
        $totalKeys = $project->totalKeysCount();
        $activeKeys = $project->activeKeysCount();
        $usedKeys = $project->usedKeysCount();
        $availableKeys = $project->availableKeysCount();
        
        return view('user.projects.show', compact(
            'project', 
            'totalKeys', 
            'activeKeys', 
            'usedKeys', 
            'availableKeys'
        ));
    }

    /**
     * Affiche le formulaire d'édition d'un projet.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $project = $user->projects()->findOrFail($id);
        
        return view('user.projects.edit', compact('project'));
    }

    /**
     * Met à jour un projet.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url|max:255',
            'is_active' => 'required|in:true,false',
        ]);

        $user = Auth::user();
        $project = $user->projects()->findOrFail($id);
        
        try {
            $project->name = $request->name;
            $project->description = $request->description;
            $project->website_url = $request->website_url;
            $project->is_active = $request->is_active;
            $project->save();
            
            Log::info('Projet mis à jour avec succès', [
                'user_id' => $user->id,
                'project_id' => $project->id
            ]);
            
            return redirect()->route('user.projects.show', $project->id)
                ->with('success', 'Projet mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du projet', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du projet.');
        }
    }

    /**
     * Supprime un projet.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $project = $user->projects()->findOrFail($id);
        
        try {
            // Vérifier si le projet a des clés actives
            if ($project->activeKeysCount() > 0) {
                return back()->with('error', 'Impossible de supprimer ce projet car il contient des clés actives.');
            }
            
            $project->delete();
            
            Log::info('Projet supprimé avec succès', [
                'user_id' => $user->id,
                'project_id' => $id
            ]);
            
            return redirect()->route('user.projects.index')
                ->with('success', 'Projet supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du projet', [
                'user_id' => $user->id,
                'project_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de la suppression du projet.');
        }
    }
    
    /**
     * Génère des clés pour un projet.
     */
    public function generateKeys(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $user = Auth::user();
        $project = $user->projects()->findOrFail($id);
        
        try {
            $quantity = $request->quantity;
            $keys = [];
            
            for ($i = 0; $i < $quantity; $i++) {
                $serialKey = new \App\Models\SerialKey();
                $serialKey->project_id = $project->id;
                $serialKey->key = strtoupper(Str::random(5) . '-' . Str::random(5) . '-' . Str::random(5) . '-' . Str::random(5));
                $serialKey->is_active = true;
                $serialKey->save();
                
                $keys[] = $serialKey;
            }
            
            Log::info('Clés générées avec succès', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'quantity' => $quantity
            ]);
            
            return redirect()->route('user.projects.show', $project->id)
                ->with('success', $quantity . ' clés générées avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération des clés', [
                'user_id' => $user->id,
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de la génération des clés.');
        }
    }
}
