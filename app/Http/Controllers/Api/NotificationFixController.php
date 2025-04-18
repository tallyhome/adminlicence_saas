<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationFixController extends Controller
{
    /**
     * Solution radicale pour marquer une notification comme lue
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        try {
            Log::info('Tentative de marquer la notification comme lue', ['id' => $id]);
            
            $notification = Notification::find($id);
            
            if (!$notification) {
                Log::warning('Notification non trouvée', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Notification non trouvée'
                ], 404);
            }
            
            // Marquer comme lue
            $notification->read_at = now();
            $notification->save();
            
            Log::info('Notification marquée comme lue avec succès', ['id' => $id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de la notification', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Solution radicale pour marquer toutes les notifications comme lues
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        try {
            Log::info('Tentative de marquer toutes les notifications comme lues');
            
            // Marquer toutes les notifications non lues comme lues
            $count = Notification::whereNull('read_at')->update(['read_at' => now()]);
            
            Log::info('Toutes les notifications ont été marquées comme lues', ['count' => $count]);
            
            return response()->json([
                'success' => true,
                'message' => $count . ' notifications ont été marquées comme lues'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de toutes les notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
