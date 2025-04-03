<?php

namespace App\Services;

use App\Models\LicenceHistory;
use App\Models\SerialKey;
use App\Notifications\LicenceStatusChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class LicenceHistoryService
{
    /**
     * Enregistrer un changement de statut dans l'historique.
     *
     * @param SerialKey $serialKey
     * @param string $status
     * @return void
     */
    public function logStatusChange(SerialKey $serialKey, string $status): void
    {
        LicenceHistory::create([
            'serial_key_id' => $serialKey->id,
            'action' => $status,
            'details' => [
                'old_status' => $serialKey->getOriginal('status'),
                'new_status' => $status,
                'changed_at' => now()->toDateTimeString()
            ]
        ]);
    }

    /**
     * Enregistrer une action dans l'historique.
     *
     * @param SerialKey $serialKey
     * @param string $action
     * @param array $details
     * @return void
     */
    public function logAction(SerialKey $serialKey, string $action, array $details = []): void
    {
        LicenceHistory::create([
            'serial_key_id' => $serialKey->id,
            'action' => $action,
            'details' => $details
        ]);
    }

    /**
     * Envoyer une notification de changement de statut.
     *
     * @param SerialKey $serialKey
     * @param string $action
     * @return void
     */
    protected function sendStatusChangeNotification(SerialKey $serialKey, string $action): void
    {
        $admins = User::where('is_admin', true)->get();
        Notification::send($admins, new LicenceStatusChanged($serialKey, $action));
    }
}