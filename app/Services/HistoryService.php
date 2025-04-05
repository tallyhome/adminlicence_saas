<?php

namespace App\Services;

use App\Models\SerialKey;
use App\Models\SerialKeyHistory;
use Illuminate\Support\Facades\Auth;

class HistoryService
{
    /**
     * Enregistre une action dans l'historique
     *
     * @param SerialKey $serialKey
     * @param string $action
     * @param string|null $details
     * @return SerialKeyHistory
     */
    public function logAction(SerialKey $serialKey, string $action, ?string $details = null): SerialKeyHistory
    {
        $adminId = Auth::guard('admin')->check() ? Auth::guard('admin')->id() : null;
        
        return SerialKeyHistory::create([
            'serial_key_id' => $serialKey->id,
            'action' => $action,
            'details' => $details,
            'admin_id' => $adminId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Récupère l'historique d'une clé de licence
     *
     * @param SerialKey $serialKey
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHistory(SerialKey $serialKey)
    {
        return $serialKey->history()
            ->with('admin')
            ->latest()
            ->get();
    }
} 