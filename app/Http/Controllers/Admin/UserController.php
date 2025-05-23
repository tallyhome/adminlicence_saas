<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Affiche le formulaire de création d'un nouvel utilisateur
     */
    public function create()
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé.');
        }
        
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    /**
     * Enregistre un nouvel utilisateur créé par un admin
     */
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé.');
        }
        
        $admin = Auth::guard('admin')->user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
        ]);
        
        // Créer l'utilisateur avec l'admin_id de l'admin connecté (multi-tenant)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin_id' => $admin->id,
        ]);
        
        // Attribuer les rôles sélectionnés
        if ($request->has('roles')) {
            $user->roles()->attach($request->roles);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }
    
    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit(User $user)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé.');
        }
        
        $admin = Auth::guard('admin')->user();
        
        // Vérifier si l'admin a le droit de modifier cet utilisateur
        if (!$admin->is_super_admin && $user->admin_id !== $admin->id) {
            abort(403, 'Vous n\'avez pas le droit de modifier cet utilisateur.');
        }
        
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }
    
    /**
     * Met à jour un utilisateur existant
     */
    public function update(Request $request, User $user)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé.');
        }
        
        $admin = Auth::guard('admin')->user();
        
        // Vérifier si l'admin a le droit de modifier cet utilisateur
        if (!$admin->is_super_admin && $user->admin_id !== $admin->id) {
            abort(403, 'Vous n\'avez pas le droit de modifier cet utilisateur.');
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'subscription_ends_at' => ['nullable', 'date'],
        ]);
        
        // Mettre à jour les informations de l'utilisateur
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        // Mettre à jour les rôles
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }
        
        // Gérer l'abonnement
        if ($request->filled('plan_id')) {
            // Récupérer le plan
            $plan = \App\Models\Plan::findOrFail($request->plan_id);
            
            // Vérifier si l'utilisateur a déjà un abonnement
            $subscription = $user->subscription;
            
            if ($subscription) {
                // Mettre à jour l'abonnement existant
                $subscription->plan_id = $plan->id;
                
                // Mettre à jour la date de fin si elle est fournie
                if ($request->filled('subscription_ends_at')) {
                    $subscription->ends_at = $request->subscription_ends_at;
                } else {
                    $subscription->ends_at = null; // Abonnement sans date de fin
                }
                
                // S'assurer que l'abonnement est actif
                $subscription->status = 'active';
                if (!$subscription->starts_at) {
                    $subscription->starts_at = now();
                }
                
                $subscription->save();
            } else {
                // Créer un nouvel abonnement
                $subscription = new \App\Models\Subscription([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => $request->filled('subscription_ends_at') ? $request->subscription_ends_at : null,
                ]);
                
                $subscription->save();
            }
        } else if ($user->subscription) {
            // Si aucun plan n'est sélectionné mais que l'utilisateur a un abonnement, le désactiver
            $user->subscription->status = 'cancelled';
            $user->subscription->cancelled_at = now();
            $user->subscription->save();
        }
        
        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }
    
    /**
     * Supprime un utilisateur
     */
    public function destroy(User $user)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé.');
        }
        
        $admin = Auth::guard('admin')->user();
        
        // Vérifier si l'admin a le droit de supprimer cet utilisateur
        if (!$admin->is_super_admin && $user->admin_id !== $admin->id) {
            abort(403, 'Vous n\'avez pas le droit de supprimer cet utilisateur.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Affiche la liste des utilisateurs
     */
    public function index(Request $request)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé.');
        }
        
        $admin = Auth::guard('admin')->user();
        
        // Récupérer les superadmins et les admins
        $superadmins = Admin::where('is_super_admin', true)->paginate(10, ['*'], 'superadmins_page');
        $admins = Admin::where('is_super_admin', false)->paginate(10, ['*'], 'admins_page');
        
        // Si c'est un super admin, il voit tous les utilisateurs
        if ($admin->is_super_admin) {
            $users = User::paginate(10, ['*'], 'users_page');
        } else {
            // Sinon, il ne voit que ses utilisateurs (multi-tenant)
            $users = User::where('admin_id', $admin->id)->paginate(10, ['*'], 'users_page');
        }
        
        return view('admin.users.index', compact('users', 'superadmins', 'admins'));
    }
    
    /**
     * Affiche les détails d'un utilisateur
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé.');
        }
        
        $admin = Auth::guard('admin')->user();
        $user = User::findOrFail($id);
        
        // Vérifier si l'admin a le droit de voir cet utilisateur
        if (!$admin->is_super_admin && $user->admin_id !== $admin->id) {
            abort(403, 'Vous n\'avez pas le droit de voir les détails de cet utilisateur.');
        }
        
        // Récupérer les données avec gestion des erreurs
        try {
            $subscription = $user->subscription;
        } catch (\Exception $e) {
            $subscription = null;
        }
        
        try {
            $invoices = $user->invoices()->latest()->take(5)->get();
        } catch (\Exception $e) {
            $invoices = collect();
        }
        
        try {
            $tickets = $user->tickets()->latest()->take(5)->get();
        } catch (\Exception $e) {
            $tickets = collect();
        }
        
        return view('admin.users.user_details', compact('user', 'subscription', 'invoices', 'tickets'));
    }
}
