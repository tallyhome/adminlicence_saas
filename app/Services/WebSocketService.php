<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\SerialKey;
use App\Models\SupportTicket;
use App\Models\Invoice;
use App\Notifications\LicenceStatusChanged as LicenceStatusChangedNotification;
use App\Events\LicenceStatusChanged as LicenceStatusChangedEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class WebSocketService
{
    /**
     * Envoie une notification de changement de statut de licence
     *
     * @param SerialKey $serialKey
     * @param string $action
     * @return void
     */
    public function notifyLicenceStatusChange(SerialKey $serialKey, string $action): void
    {
        try {
            // Envoyer des notifications par email aux administrateurs
            $admins = Admin::all();
            
            foreach ($admins as $admin) {
                $admin->notify(new LicenceStatusChangedNotification($serialKey, $action));
            }
            
            // Diffuser l'événement via WebSocket
            Event::dispatch(new LicenceStatusChangedEvent($serialKey, $action));
            
            Log::info('Notification WebSocket envoyée pour la licence ' . $serialKey->serial_key);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification WebSocket: " . $e->getMessage());
        }
    }
    
    /**
     * Envoie une notification pour un nouveau ticket de support
     *
     * @param SupportTicket $ticket
     * @return void
     */
    public function notifyNewSupportTicket(SupportTicket $ticket): void
    {
        try {
            // Diffuser l'événement via WebSocket
            Event::dispatch(new \App\Events\NewSupportTicket($ticket));
            
            Log::info('Notification WebSocket envoyée pour le nouveau ticket #' . $ticket->id);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification WebSocket: " . $e->getMessage());
        }
    }
    
    /**
     * Envoie une notification pour un changement de statut d'un ticket de support
     *
     * @param SupportTicket $ticket
     * @param string $status
     * @return void
     */
    public function notifySupportTicketStatusChange(SupportTicket $ticket, string $status): void
    {
        try {
            // Envoyer des notifications par email aux administrateurs
            $admins = Admin::all();
            
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\SupportTicketStatusChanged($ticket, $status));
            }
            
            // Diffuser l'événement via WebSocket
            Event::dispatch(new \App\Events\SupportTicketStatusChanged($ticket, $status));
            
            Log::info('Notification WebSocket envoyée pour le changement de statut du ticket #' . $ticket->id);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification WebSocket: " . $e->getMessage());
        }
    }
    
    /**
     * Envoie une notification pour un nouveau paiement
     *
     * @param Invoice $invoice
     * @return void
     */
    public function notifyNewPayment(Invoice $invoice): void
    {
        try {
            // Envoyer des notifications par email aux administrateurs
            $admins = Admin::all();
            
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewPayment($invoice));
            }
            
            // Diffuser l'événement via WebSocket
            Event::dispatch(new \App\Events\NewPayment($invoice));
            
            Log::info('Notification WebSocket envoyée pour le nouveau paiement de la facture #' . $invoice->number);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification WebSocket: " . $e->getMessage());
        }
    }
}