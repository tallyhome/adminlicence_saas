<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Afficher toutes les notifications de l'utilisateur connecté
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(15);
        
        return view('admin.notifications.index', compact('notifications'));
    }
    
    /**
     * Récupérer les notifications non lues
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnread()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications;
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }
    
    /**
     * Marquer une notification comme lue
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $notification = DatabaseNotification::find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
    
    /**
     * Marquer toutes les notifications comme lues
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Supprimer une notification
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $notification = DatabaseNotification::find($id);
        
        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
    
    /**
     * Mettre à jour les préférences de notification de l'utilisateur
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $preferences = $request->validate([
            'licence_status' => 'boolean',
            'support_tickets' => 'boolean',
            'payments' => 'boolean',
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean'
        ]);
        
        $user->notification_preferences = $preferences;
        $user->save();
        
        return response()->json([
            'success' => true,
            'preferences' => $user->notification_preferences
        ]);
    }
}