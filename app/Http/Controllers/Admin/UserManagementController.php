<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\User;

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
            $superadmins = collect([]);
            $admins = collect([$admin]);
            $users = User::where('admin_id', $admin->id)->paginate(10, ['*'], 'users');
            
            return view('admin.users.index', compact('superadmins', 'admins', 'users'));
        }
    }
    
    /**
     * Affiche les détails d'un utilisateur spécifique
     */
    public function show($id)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé. Vous devez être connecté en tant qu\'administrateur.');
        }
        
        $admin = Auth::guard('admin')->user();
        $user = User::findOrFail($id);
        
        // Vérifier que l'admin a le droit de voir cet utilisateur (multi-tenant)
        if (!$admin->is_super_admin && $user->admin_id !== $admin->id) {
            abort(403, 'Vous n\'avez pas accès à cet utilisateur.');
        }
        
        // Récupérer les informations supplémentaires de l'utilisateur
        $subscription = $user->subscription;
        $tickets = $user->tickets()->latest()->take(5)->get();
        $invoices = $user->invoices()->latest()->take(5)->get();
        
        return view('admin.users.show', compact('user', 'subscription', 'tickets', 'invoices'));
    }
}
