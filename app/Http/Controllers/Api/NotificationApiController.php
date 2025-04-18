<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends Controller
{
    /**
     * Marquer une notification comme lue
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            
            // Marquer comme lue si ce n'est pas déjà fait
            if (!$notification->read_at) {
                $notification->read_at = now();
                $notification->save();
            }
            
            return response()->json(['success' => true, 'message' => 'Notification marquée comme lue']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Marquer toutes les notifications comme lues
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        try {
            $admin = Auth::guard('admin')->user();
            $now = now();
            
            if ($admin->is_super_admin) {
                // Les superadmins peuvent marquer toutes les notifications comme lues
                Notification::whereNull('read_at')->update(['read_at' => $now]);
            } else {
                // Les admins normaux ne peuvent marquer que leurs notifications comme lues
                Notification::whereNull('read_at')
                    ->where(function($query) use ($admin) {
                        $query->where('target_type', 'all')
                            ->orWhere('target_type', 'admins')
                            ->orWhere(function($q) use ($admin) {
                                $q->where('target_type', 'specific')
                                    ->whereJsonContains('target_ids', (string)$admin->id);
                        });
                    })
                    ->update(['read_at' => $now]);
            }
            
            return response()->json(['success' => true, 'message' => 'Toutes les notifications ont été marquées comme lues']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
}
