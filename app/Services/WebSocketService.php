<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Notification;
use App\Models\SerialKey;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\LicenceStatusChanged as LicenceStatusChangedNotification;
use App\Events\LicenceStatusChanged as LicenceStatusChangedEvent;
use App\Events\NotificationReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class WebSocketService
{
    /**
     * Diffuse une notification via WebSocket
     *
     * @param Notification $notification
     * @return void
     */
    public function broadcastNotification(Notification $notification): void
    {
        try {
            // Déterminer les destinataires de la notification
            $recipients = $this->getNotificationRecipients($notification);
            
            // Diffuser l'événement via WebSocket pour chaque destinataire
            foreach ($recipients as $recipient) {
                Event::dispatch(new NotificationReceived($notification, $recipient));
            }
            
            Log::info('Notification WebSocket diffusée: ' . $notification->title);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la diffusion de la notification WebSocket: " . $e->getMessage());
        }
    }
    
    /**
     * Détermine les destinataires d'une notification
     *
     * @param Notification $notification
     * @return array
     */
    private function getNotificationRecipients(Notification $notification): array
    {
        $recipients = [];
        
        switch ($notification->target_type) {
            case 'all':
                $admins = Admin::all();
                $users = User::all();
                $recipients = array_merge($admins->toArray(), $users->toArray());
                break;
                
            case 'admins':
                $recipients = Admin::all()->toArray();
                break;
                
            case 'users':
                $recipients = User::all()->toArray();
                break;
                
            case 'specific':
                if ($notification->target_ids) {
                    $adminRecipients = Admin::whereIn('id', $notification->target_ids)->get()->toArray();
                    $userRecipients = User::whereIn('id', $notification->target_ids)->get()->toArray();
                    $recipients = array_merge($adminRecipients, $userRecipients);
                }
                break;
        }
        
        return $recipients;
    }
    
    /**
     * Notifie les changements d'un abonnement
     *
     * @param Subscription $subscription
     * @return void
     */
    public function notifySubscriptionUpdated(Subscription $subscription): void
    {
        try {
            // Créer une notification dans la base de données
            $notification = new Notification();
            $notification->title = 'Mise à jour d\'abonnement';
            $notification->message = 'L\'abonnement #' . $subscription->id . ' a été mis à jour. Statut: ' . $subscription->status;
            $notification->type = 'info';
            $notification->target_type = 'admins'; // Notifier tous les administrateurs
            $notification->sender_type = 'system';
            $notification->metadata = [
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'tenant_id' => $subscription->tenant_id
            ];
            $notification->save();
            
            // Diffuser via WebSocket
            $this->broadcastNotification($notification);
            
            Log::info('Notification de mise à jour d\'abonnement envoyée pour l\'abonnement #' . $subscription->id);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification d'abonnement: " . $e->getMessage());
        }
    }
    
    /**
     * Notifie un échec de paiement
     *
     * @param Invoice $invoice
     * @return void
     */
    public function notifyPaymentFailed(Invoice $invoice): void
    {
        try {
            // Créer une notification dans la base de données
            $notification = new Notification();
            $notification->title = 'Échec de paiement';
            $notification->message = 'Le paiement de la facture #' . $invoice->number . ' a échoué.';
            $notification->type = 'error';
            $notification->target_type = 'admins'; // Notifier tous les administrateurs
            $notification->sender_type = 'system';
            $notification->metadata = [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
                'tenant_id' => $invoice->tenant_id
            ];
            $notification->save();
            
            // Diffuser via WebSocket
            $this->broadcastNotification($notification);
            
            Log::info('Notification d\'échec de paiement envoyée pour la facture #' . $invoice->number);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification d'échec de paiement: " . $e->getMessage());
        }
    }
    
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