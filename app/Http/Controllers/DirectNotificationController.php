<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DirectNotificationController extends Controller
{
    /**
     * Mise à jour directe d'une notification
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            
            if ($id === 'all') {
                // Marquer toutes les notifications comme lues
                DB::table('notifications')
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
                
                // Rediriger vers la page des notifications avec paramètre de rafraîchissement
                return redirect('/admin/notifications?refresh=' . time())->with('success', 'Toutes les notifications ont été marquées comme lues');
            } else {
                // Marquer une notification spécifique comme lue
                DB::table('notifications')
                    ->where('id', $id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
                
                // Rediriger vers la page des notifications avec paramètre de rafraîchissement
                return redirect('/admin/notifications?refresh=' . time())->with('success', 'Notification marquée comme lue');
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour directe de la notification', [
                'id' => $request->input('id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour de la notification');
        }
    }
}
