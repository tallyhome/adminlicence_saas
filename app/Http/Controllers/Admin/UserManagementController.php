<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\User;
use App\Models\Project;
use App\Models\Product;
use App\Models\Licence;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    /**
     * Affiche la liste des utilisateurs en fonction du rôle de l'admin connecté
     * - Superadmin: voit tous les utilisateurs, admins et superadmins
     * - Admin: voit uniquement les utilisateurs liés à son compte (multi-tenant)
     */
    public function index(Request $request)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé. Vous devez être connecté en tant qu\'administrateur.');
        }
        
        $admin = Auth::guard('admin')->user();
        
        if ($admin->is_super_admin) {
            // Le superadmin voit tout le monde
            $superadmins = Admin::where('is_super_admin', true)->paginate(10, ['*'], 'superadmins');
            $admins = Admin::where('is_super_admin', false)->paginate(10, ['*'], 'admins');
            $users = User::paginate(10, ['*'], 'users');
            
            return view('admin.users.index', compact('superadmins', 'admins', 'users'));
        } else {
            // L'admin normal voit uniquement ses utilisateurs (multi-tenant)
            // Utiliser des objets de pagination vides au lieu de collections
            $superadmins = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['path' => request()->url(), 'pageName' => 'superadmins']);
            // Pour les admins, on crée une pagination avec l'admin actuel
            $admins = new \Illuminate\Pagination\LengthAwarePaginator([$admin], 1, 10, 1, ['path' => request()->url(), 'pageName' => 'admins']);
            $users = User::where('admin_id', $admin->id)->paginate(10, ['*'], 'users');
            
            return view('admin.users.index', compact('superadmins', 'admins', 'users'));
        }
    }
    
    /**
     * Affiche les détails d'un utilisateur ou d'un administrateur
     *
     * @param int $id ID de l'utilisateur ou de l'administrateur
     * @param string $type Type d'utilisateur ('user' ou 'admin')
     * @return \Illuminate\View\View
     */
    public function show($id, $type = 'user')
    {
        try {
            // Vérifier explicitement le type d'entité (admin ou user) en fonction de l'ID
            // Utiliser des tables différentes pour les admins et les utilisateurs
            $adminExists = Admin::where('id', $id)->exists();
            $userExists = User::where('id', $id)->exists();
            
            // Si l'ID correspond à un administrateur, afficher les détails de l'admin
            if ($adminExists && $type === 'admin') {
                return $this->showAdmin($id);
            }
            
            // Si l'ID correspond à un utilisateur normal
            if ($userExists) {
                $user = User::with('roles')->findOrFail($id);
                
                // Vérifier si l'admin connecté a accès à cet utilisateur (multi-tenant)
                if (Auth::guard('admin')->user()->is_super_admin == false && $user->admin_id != Auth::guard('admin')->id()) {
                    abort(403, 'Vous n\'avez pas accès à cet utilisateur.');
                }
            } else {
                abort(404, 'Utilisateur non trouvé.');
            }
            
            // Vérifier explicitement si c'est un utilisateur normal ou un superadmin
            $is_super_admin = false; // Par défaut, c'est un utilisateur normal
            
            // Récupérer l'abonnement réel de l'utilisateur
            $subscription = Subscription::where('user_id', $user->id)->first();
            $plan = null;
            
            if ($subscription) {
                $plan = Plan::find($subscription->plan_id);
            }
            
            // Récupérer les projets récents
            $projects = $user->projects()
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
            $projectsCount = $user->projects()->count();
            
            // Récupérer les produits récents
            $products = $user->products()
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
            $productsCount = $user->products()->count();
            
            // Récupérer les licences récentes
            $licences = $user->licences()
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
            $licencesCount = $user->licences()->count();
            
            // Récupérer les factures récentes
            $invoices = Invoice::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
            
            return view('admin.users.show', compact(
                'user', 
                'subscription', 
                'plan', 
                'invoices', 
                'projects', 
                'projectsCount', 
                'products', 
                'productsCount', 
                'licences', 
                'licencesCount'
            ));
        } catch (\Exception $e) {
            // Journaliser l'erreur
            Log::error('Erreur lors de l\'affichage des détails de l\'utilisateur', [
                'admin_id' => Auth::guard('admin')->id(),
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            // Rediriger avec un message d'erreur
            return redirect()->route('admin.users.index')
                ->with('error', 'Une erreur est survenue lors de l\'affichage des détails de l\'utilisateur: ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche les projets d'un utilisateur
     *
     * @param int $id ID de l'utilisateur
     * @return \Illuminate\View\View
     */
    public function userProjects($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Vérifier si l'admin connecté a accès à cet utilisateur (multi-tenant)
            if (Auth::guard('admin')->user()->is_super_admin == false && $user->admin_id != Auth::guard('admin')->id()) {
                abort(403, 'Vous n\'avez pas accès à cet utilisateur.');
            }
            
            $projects = $user->projects()->paginate(15);
            
            return view('admin.users.projects', compact('user', 'projects'));
        } catch (\Exception $e) {
            // Journaliser l'erreur
            Log::error('Erreur lors de l\'affichage des projets de l\'utilisateur', [
                'admin_id' => Auth::guard('admin')->id(),
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            // Rediriger avec un message d'erreur
            return redirect()->route('admin.users.show', $id)
                ->with('error', 'Une erreur est survenue lors de l\'affichage des projets: ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche les produits d'un utilisateur
     *
     * @param int $id ID de l'utilisateur
     * @return \Illuminate\View\View
     */
    public function userProducts($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Vérifier si l'admin connecté a accès à cet utilisateur (multi-tenant)
            if (Auth::guard('admin')->user()->is_super_admin == false && $user->admin_id != Auth::guard('admin')->id()) {
                abort(403, 'Vous n\'avez pas accès à cet utilisateur.');
            }
            
            $products = $user->products()->paginate(15);
            
            return view('admin.users.products', compact('user', 'products'));
        } catch (\Exception $e) {
            // Journaliser l'erreur
            Log::error('Erreur lors de l\'affichage des produits de l\'utilisateur', [
                'admin_id' => Auth::guard('admin')->id(),
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            // Rediriger avec un message d'erreur
            return redirect()->route('admin.users.show', $id)
                ->with('error', 'Une erreur est survenue lors de l\'affichage des produits: ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche les licences d'un utilisateur
     *
     * @param int $id ID de l'utilisateur
     * @return \Illuminate\View\View
     */
    public function userLicences($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Vérifier si l'admin connecté a accès à cet utilisateur (multi-tenant)
            if (Auth::guard('admin')->user()->is_super_admin == false && $user->admin_id != Auth::guard('admin')->id()) {
                abort(403, 'Vous n\'avez pas accès à cet utilisateur.');
            }
            
            $licences = $user->licences()->paginate(15);
            
            return view('admin.users.licences', compact('user', 'licences'));
        } catch (\Exception $e) {
            // Journaliser l'erreur
            Log::error('Erreur lors de l\'affichage des licences de l\'utilisateur', [
                'admin_id' => Auth::guard('admin')->id(),
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            // Rediriger avec un message d'erreur
            return redirect()->route('admin.users.show', $id)
                ->with('error', 'Une erreur est survenue lors de l\'affichage des licences: ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche les détails d'un administrateur
     *
     * @param int $id ID de l'administrateur
     * @return \Illuminate\View\View
     */
    public function showAdmin($id)
    {
        try {
            // Récupérer l'administrateur
            $admin = Admin::findOrFail($id);
            
            // Vérifier si l'utilisateur connecté est un super admin
            // Seul un super admin peut voir les détails d'un autre admin
            if (!Auth::guard('admin')->user()->is_super_admin && Auth::guard('admin')->id() != $id) {
                abort(403, 'Vous n\'avez pas accès à cet administrateur.');
            }
            
            // Récupérer les statistiques de l'administrateur
            $userCount = User::where('admin_id', $admin->id)->count();
            
            // Récupérer les utilisateurs gérés par cet administrateur
            $managedUsers = User::where('admin_id', $admin->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            // Récupérer les rôles de l'administrateur
            $roles = $admin->roles;
            
            return view('admin.users.admin_details', compact('admin', 'userCount', 'managedUsers', 'roles'));
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            Log::error('Erreur lors de l\'affichage des détails de l\'administrateur', [
                'admin_id' => Auth::guard('admin')->id(),
                'error' => $e->getMessage()
            ]);
            
            // Rediriger avec un message d'erreur
            return redirect()->route('admin.users.index')
                ->with('error', 'Une erreur est survenue lors de l\'affichage des détails de l\'administrateur: ' . $e->getMessage());
        }
    }
    
    /**
     * Met à jour l'abonnement d'un utilisateur
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID de l'utilisateur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUserSubscription(Request $request, $id)
    {
        try {
            // Récupérer l'utilisateur
            $user = User::findOrFail($id);
            
            // Vérifier si l'admin connecté a accès à cet utilisateur (multi-tenant)
            if (Auth::guard('admin')->user()->is_super_admin == false && $user->admin_id != Auth::guard('admin')->id()) {
                abort(403, 'Vous n\'avez pas accès à cet utilisateur.');
            }
            
            // Valider les données
            $validated = $request->validate([
                'plan_id' => 'required|exists:plans,id',
                'status' => 'required|in:active,inactive,cancelled',
                'ends_at' => 'nullable|date',
            ]);
            
            // Récupérer l'abonnement existant ou en créer un nouveau
            $subscription = Subscription::where('user_id', $user->id)->first();
            
            if (!$subscription) {
                $subscription = new Subscription();
                $subscription->user_id = $user->id;
            }
            
            // Mettre à jour les informations d'abonnement
            $subscription->plan_id = $validated['plan_id'];
            $subscription->status = $validated['status'];
            
            if (isset($validated['ends_at'])) {
                $subscription->ends_at = $validated['ends_at'];
            }
            
            // Sauvegarder les modifications
            $subscription->save();
            
            Log::info('Abonnement utilisateur mis à jour par admin', [
                'admin_id' => Auth::guard('admin')->id(),
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'plan_id' => $subscription->plan_id,
                'status' => $subscription->status
            ]);
            
            // Rediriger avec un message de succès
            return redirect()->route('admin.users.show', $id)
                ->with('success', 'L\'abonnement de l\'utilisateur a été mis à jour avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'abonnement utilisateur', [
                'admin_id' => Auth::guard('admin')->id(),
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            // En cas d'erreur, rediriger avec un message d'erreur
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'abonnement: ' . $e->getMessage());
        }
    }
    
    /**
     * Met à jour les informations d'un administrateur
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID de l'administrateur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAdmin(Request $request, $id)
    {
        try {
            // Récupérer l'administrateur
            $admin = Admin::findOrFail($id);
            
            // Vérifier les permissions (seul un super admin peut modifier un autre admin)
            if (!Auth::guard('admin')->user()->is_super_admin && Auth::guard('admin')->id() != $id) {
                abort(403, 'Vous n\'avez pas les permissions nécessaires pour modifier cet administrateur.');
            }
            
            // Valider les données
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:admins,email,' . $id,
                'password' => 'nullable|string|min:8|confirmed',
                'is_super_admin' => 'nullable|boolean',
            ]);
            
            // Mettre à jour les informations de base
            $admin->name = $validated['name'];
            $admin->email = $validated['email'];
            
            // Mettre à jour le mot de passe si fourni
            if (!empty($validated['password'])) {
                $admin->password = bcrypt($validated['password']);
            }
            
            // Mettre à jour le statut de super admin si l'utilisateur est un super admin
            if (Auth::guard('admin')->user()->is_super_admin && isset($validated['is_super_admin'])) {
                $admin->is_super_admin = (bool) $validated['is_super_admin'];
            }
            
            // Sauvegarder les modifications
            $admin->save();
            
            // Rediriger avec un message de succès
            return redirect()->route('admin.users.show', $id)
                ->with('success', 'Les informations de l\'administrateur ont été mises à jour avec succès.');
                
        } catch (\Exception $e) {
            // En cas d'erreur, rediriger avec un message d'erreur
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'administrateur: ' . $e->getMessage());
        }
    }
}
