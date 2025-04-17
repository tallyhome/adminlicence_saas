<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de pagination
        $perPage = $request->input('per_page', 10);
        $validPerPage = in_array($perPage, [10, 25, 50, 100, 500, 1000]) ? $perPage : 10;
        
        $notifications = Notification::orderBy('created_at', 'desc')->paginate($validPerPage);
        
        // Utilise le nouveau dashboard en fonction du rôle de l'utilisateur
        $admin = Auth::guard('admin')->user();
        if ($admin->is_super_admin) {
            return view('admin.dashboard_superadmin', compact('notifications', 'validPerPage'));
        } else {
            return view('admin.dashboard_user', compact('notifications', 'validPerPage'));
        }
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        // Vérifier si l'utilisateur est superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'Seuls les superadmins peuvent créer des notifications.');
        }
        
        $admins = Admin::where('is_super_admin', false)->get();
        $users = User::all();
        
        return view('admin.notifications.create', compact('admins', 'users'));
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'Seuls les superadmins peuvent créer des notifications.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_type' => 'required|in:all,admins,users,specific',
            'target_ids' => 'required_if:target_type,specific|array',
        ]);
        
        $notification = new Notification();
        $notification->title = $request->title;
        $notification->message = $request->message;
        $notification->sender_id = Auth::guard('admin')->id();
        $notification->sender_type = 'admin';
        
        // Déterminer les destinataires
        switch ($request->target_type) {
            case 'all':
                $notification->target_type = 'all';
                break;
            case 'admins':
                $notification->target_type = 'admins';
                break;
            case 'users':
                $notification->target_type = 'users';
                break;
            case 'specific':
                $notification->target_type = 'specific';
                $notification->target_ids = $request->target_ids;
                break;
        }
        
        $notification->save();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification créée et envoyée avec succès.');
    }

    /**
     * Get unread notifications for the current user.
     */
    public function getUnread()
    {
        $admin = Auth::guard('admin')->user();
        $notifications = [];
        
        // Récupérer les notifications pour cet admin
        if ($admin->is_super_admin) {
            // Les superadmins voient toutes les notifications
            $notifications = Notification::where('read', false)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Les admins normaux voient les notifications qui leur sont destinées
            $notifications = Notification::where('read', false)
                ->where(function($query) use ($admin) {
                    $query->where('target_type', 'all')
                        ->orWhere('target_type', 'admins')
                        ->orWhere(function($q) use ($admin) {
                            $q->where('target_type', 'specific')
                                ->whereJsonContains('target_ids', $admin->id);
                        });
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return response()->json($notifications);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->read = true;
        $notification->save();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $admin = Auth::guard('admin')->user();
        
        if ($admin->is_super_admin) {
            // Les superadmins peuvent marquer toutes les notifications comme lues
            Notification::where('read', false)->update(['read' => true]);
        } else {
            // Les admins normaux ne peuvent marquer que leurs notifications comme lues
            Notification::where('read', false)
                ->where(function($query) use ($admin) {
                    $query->where('target_type', 'all')
                        ->orWhere('target_type', 'admins')
                        ->orWhere(function($q) use ($admin) {
                            $q->where('target_type', 'specific')
                                ->whereJsonContains('target_ids', $admin->id);
                        });
                })
                ->update(['read' => true]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy($id)
    {
        // Vérifier si l'utilisateur est superadmin
        if (!Auth::guard('admin')->user()->is_super_admin) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'Seuls les superadmins peuvent supprimer des notifications.');
        }
        
        $notification = Notification::findOrFail($id);
        $notification->delete();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $admin->notification_preferences = $request->preferences;
        $admin->save();
        
        return response()->json(['success' => true]);
    }
}
