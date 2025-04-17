<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Envoyer une notification à des destinataires spécifiques
     *
     * @param string $title Titre de la notification
     * @param string $message Contenu de la notification
     * @param string $type Type de notification (info, success, warning, error)
     * @param string $targetType Type de destinataire (all, admins, users, specific)
     * @param array|null $targetIds IDs des destinataires spécifiques
     * @param int|null $senderId ID de l'expéditeur
     * @param string|null $senderType Type d'expéditeur (admin, system)
     * @param array|null $metadata Données supplémentaires pour la notification
     * @return Notification
     */
    public function send(
        string $title,
        string $message,
        string $type = 'info',
        string $targetType = 'all',
        ?array $targetIds = null,
        ?int $senderId = null,
        ?string $senderType = 'system',
        ?array $metadata = null
    ): Notification {
        try {
            $notification = new Notification();
            $notification->title = $title;
            $notification->message = $message;
            $notification->type = $type;
            $notification->target_type = $targetType;
            $notification->target_ids = $targetIds;
            $notification->sender_id = $senderId;
            $notification->sender_type = $senderType;
            $notification->metadata = $metadata;
            $notification->read = false;
            $notification->save();

            // Envoyer la notification en temps réel via WebSocket si disponible
            try {
                app(WebSocketService::class)->broadcastNotification($notification);
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast notification: ' . $e->getMessage());
            }

            // Envoyer des emails pour les notifications importantes si configuré
            $this->sendEmailNotifications($notification);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envoyer une notification à tous les utilisateurs
     *
     * @param string $title Titre de la notification
     * @param string $message Contenu de la notification
     * @param string $type Type de notification
     * @param array|null $metadata Données supplémentaires
     * @return Notification
     */
    public function sendToAll(string $title, string $message, string $type = 'info', ?array $metadata = null): Notification
    {
        return $this->send($title, $message, $type, 'all', null, null, 'system', $metadata);
    }

    /**
     * Envoyer une notification à tous les administrateurs
     *
     * @param string $title Titre de la notification
     * @param string $message Contenu de la notification
     * @param string $type Type de notification
     * @param array|null $metadata Données supplémentaires
     * @return Notification
     */
    public function sendToAdmins(string $title, string $message, string $type = 'info', ?array $metadata = null): Notification
    {
        return $this->send($title, $message, $type, 'admins', null, null, 'system', $metadata);
    }

    /**
     * Envoyer une notification à tous les utilisateurs (non-admin)
     *
     * @param string $title Titre de la notification
     * @param string $message Contenu de la notification
     * @param string $type Type de notification
     * @param array|null $metadata Données supplémentaires
     * @return Notification
     */
    public function sendToUsers(string $title, string $message, string $type = 'info', ?array $metadata = null): Notification
    {
        return $this->send($title, $message, $type, 'users', null, null, 'system', $metadata);
    }

    /**
     * Envoyer une notification à un administrateur spécifique
     *
     * @param Admin $admin Administrateur destinataire
     * @param string $title Titre de la notification
     * @param string $message Contenu de la notification
     * @param string $type Type de notification
     * @param array|null $metadata Données supplémentaires
     * @return Notification
     */
    public function sendToAdmin(Admin $admin, string $title, string $message, string $type = 'info', ?array $metadata = null): Notification
    {
        return $this->send($title, $message, $type, 'specific', [$admin->id], null, 'system', $metadata);
    }

    /**
     * Envoyer une notification à un utilisateur spécifique
     *
     * @param User $user Utilisateur destinataire
     * @param string $title Titre de la notification
     * @param string $message Contenu de la notification
     * @param string $type Type de notification
     * @param array|null $metadata Données supplémentaires
     * @return Notification
     */
    public function sendToUser(User $user, string $title, string $message, string $type = 'info', ?array $metadata = null): Notification
    {
        return $this->send($title, $message, $type, 'specific', [$user->id], null, 'system', $metadata);
    }

    /**
     * Envoyer des emails pour les notifications importantes
     *
     * @param Notification $notification Notification à envoyer par email
     * @return void
     */
    private function sendEmailNotifications(Notification $notification): void
    {
        // Ne pas envoyer d'email pour les notifications de type info
        if ($notification->type === 'info') {
            return;
        }

        // Déterminer les destinataires
        $recipients = [];

        switch ($notification->target_type) {
            case 'all':
                $recipients = array_merge(
                    Admin::whereJsonContains('notification_preferences->email', $notification->type)->get()->toArray(),
                    User::whereJsonContains('notification_preferences->email', $notification->type)->get()->toArray()
                );
                break;
            case 'admins':
                $recipients = Admin::whereJsonContains('notification_preferences->email', $notification->type)->get()->toArray();
                break;
            case 'users':
                $recipients = User::whereJsonContains('notification_preferences->email', $notification->type)->get()->toArray();
                break;
            case 'specific':
                if ($notification->target_ids) {
                    $adminRecipients = Admin::whereIn('id', $notification->target_ids)
                        ->whereJsonContains('notification_preferences->email', $notification->type)
                        ->get()
                        ->toArray();
                    
                    $userRecipients = User::whereIn('id', $notification->target_ids)
                        ->whereJsonContains('notification_preferences->email', $notification->type)
                        ->get()
                        ->toArray();
                    
                    $recipients = array_merge($adminRecipients, $userRecipients);
                }
                break;
        }

        // Envoyer les emails
        foreach ($recipients as $recipient) {
            try {
                $email = $recipient['email'];
                // Utiliser le service d'email pour envoyer la notification
                app(MailService::class)->sendNotificationEmail(
                    $email,
                    $notification->title,
                    $notification->message,
                    $notification->type,
                    $notification->metadata
                );
            } catch (\Exception $e) {
                Log::error('Failed to send notification email: ' . $e->getMessage());
            }
        }
    }
}
