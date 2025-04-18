<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolutionFinaleController extends Controller
{
    /**
     * Marquer une notification comme lue
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function marquerCommeLu($id)
    {
        try {
            Log::info('Tentative de marquer la notification comme lue', ['id' => $id]);
            
            // Mise à jour directe dans la base de données
            $updated = DB::table('notifications')
                ->where('id', $id)
                ->update(['read_at' => now()]);
            
            Log::info('Notification marquée comme lue', ['id' => $id, 'updated' => $updated]);
            
            // Rediriger vers la page précédente
            return redirect()->back()->with('success', 'Notification marquée comme lue');
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de la notification', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Marquer toutes les notifications comme lues
     * 
     * @return \Illuminate\Http\Response
     */
    public function marquerToutCommeLu()
    {
        try {
            Log::info('Tentative de marquer toutes les notifications comme lues');
            
            // Mise à jour directe dans la base de données
            $count = DB::table('notifications')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            
            Log::info('Toutes les notifications ont été marquées comme lues', ['count' => $count]);
            
            // Rediriger vers la page précédente
            return redirect()->back()->with('success', $count . ' notifications ont été marquées comme lues');
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de toutes les notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
