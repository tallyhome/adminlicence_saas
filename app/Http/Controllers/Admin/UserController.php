<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
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
        
        return redirect()->route('admin.users.index')
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
}
