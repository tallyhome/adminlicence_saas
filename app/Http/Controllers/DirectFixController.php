<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DirectFixController extends Controller
{
    /**
     * Solution radicale pour marquer une notification comme lue
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function fixNotification(Request $request)
    {
        try {
            $id = $request->input('id');
            
            // Journaliser l'opération
            Log::info('Tentative de correction directe de notification', ['id' => $id]);
            
            if ($id === 'all') {
                // Marquer toutes les notifications comme lues
                $count = DB::table('notifications')
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
                
                Log::info('Toutes les notifications ont été marquées comme lues', ['count' => $count]);
                
                // Rediriger vers l'URL actuelle si spécifiée, sinon vers la page précédente
                $currentUrl = $request->input('current_url');
                $returnUrl = $request->input('return_url');
                
                if ($currentUrl) {
                    return redirect($currentUrl)->with('success', $count . ' notifications ont été marquées comme lues');
                } else if ($returnUrl) {
                    return redirect($returnUrl)->with('success', $count . ' notifications ont été marquées comme lues');
                } else {
                    return back()->with('success', $count . ' notifications ont été marquées comme lues');
                }
            } else {
                // Marquer une notification spécifique comme lue
                $updated = DB::table('notifications')
                    ->where('id', $id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
                
                Log::info('Notification marquée comme lue', ['id' => $id, 'updated' => $updated]);
                
                // Rediriger vers l'URL actuelle si spécifiée, sinon vers la page précédente
                $currentUrl = $request->input('current_url');
                $returnUrl = $request->input('return_url');
                
                if ($currentUrl) {
                    return redirect($currentUrl)->with('success', 'Notification marquée comme lue avec succès');
                } else if ($returnUrl) {
                    return redirect($returnUrl)->with('success', 'Notification marquée comme lue avec succès');
                } else {
                    return back()->with('success', 'Notification marquée comme lue avec succès');
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la correction directe de notification', [
                'id' => $request->input('id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }
}
