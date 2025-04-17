<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Vérifier si l'utilisateur est un superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour gérer les rôles.');
        }
        
        $roles = Role::withCount(['admins', 'users'])->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Vérifier si l'utilisateur est un superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour créer des rôles.');
        }
        
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est un superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour créer des rôles.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        DB::beginTransaction();
        try {
            // Créer le rôle
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            
            // Attacher les permissions
            if ($request->has('permissions')) {
                $role->permissions()->attach($request->permissions);
            }
            
            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Le rôle a été créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du rôle: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Vérifier si l'utilisateur est un superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour voir les détails des rôles.');
        }
        
        $role = Role::with('permissions')->findOrFail($id);
        $admins = Admin::whereHas('roles', function($query) use ($id) {
            $query->where('roles.id', $id);
        })->paginate(10, ['*'], 'admins');
        
        $users = User::whereHas('roles', function($query) use ($id) {
            $query->where('roles.id', $id);
        })->paginate(10, ['*'], 'users');
        
        return view('admin.roles.show', compact('role', 'admins', 'users'));
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Vérifier si l'utilisateur est un superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour modifier les rôles.');
        }
        
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Vérifier si l'utilisateur est un superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour modifier les rôles.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            
            // Mettre à jour le rôle
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            
            // Synchroniser les permissions
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->detach();
            }
            
            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Le rôle a été mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du rôle: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Vérifier si l'utilisateur est un superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour supprimer des rôles.');
        }
        
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            
            // Détacher toutes les relations avant de supprimer
            $role->permissions()->detach();
            $role->admins()->detach();
            $role->users()->detach();
            
            // Supprimer le rôle
            $role->delete();
            
            DB::commit();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Le rôle a été supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression du rôle: ' . $e->getMessage());
        }
    }
    
    /**
     * Assigner des rôles à un administrateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignRolesToAdmin(Request $request, $id)
    {
        // Vérifier si l'utilisateur est un superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour assigner des rôles.');
        }
        
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        DB::beginTransaction();
        try {
            $admin = Admin::findOrFail($id);
            
            // Synchroniser les rôles
            $admin->roles()->sync($request->roles);
            
            DB::commit();
            return redirect()->route('admin.users.show', $id)
                ->with('success', 'Les rôles ont été assignés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'assignation des rôles: ' . $e->getMessage());
        }
    }
    
    /**
     * Assigner des rôles à un utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignRolesToUser(Request $request, $id)
    {
        // Vérifier si l'utilisateur est un superadmin ou un admin
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour assigner des rôles.');
        }
        
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            
            // Vérifier si l'admin a accès à cet utilisateur (multi-tenant)
            if (!Auth::guard('admin')->user()->is_super_admin && $user->admin_id != Auth::guard('admin')->id()) {
                abort(403, 'Vous n\'avez pas accès à cet utilisateur.');
            }
            
            // Synchroniser les rôles
            $user->roles()->sync($request->roles);
            
            DB::commit();
            return redirect()->route('admin.users.show', $id)
                ->with('success', 'Les rôles ont été assignés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Une erreur est survenue lors de l\'assignation des rôles: ' . $e->getMessage());
        }
    }
}
