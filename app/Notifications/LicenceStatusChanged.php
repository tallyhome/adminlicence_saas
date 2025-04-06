<?php

namespace App\Notifications;

use App\Models\SerialKey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenceStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var SerialKey
     */
    protected $serialKey;

    /**
     * @var string
     */
    protected $action;

    /**
     * Create a new notification instance.
     */
    public function __construct(SerialKey $serialKey, string $action)
    {
        $this->serialKey = $serialKey;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'broadcast', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessages = [
            'revoked' => 'révoquée',
            'suspended' => 'suspendue',
            'expired' => 'expirée',
            'suspended' => 'suspendue'
        ];

        $status = $statusMessages[$this->action] ?? $this->action;

        return (new MailMessage)
            ->subject('Changement de statut de licence - ' . $this->serialKey->serial_key)
            ->line('La licence suivante a été ' . $status . ' :')
            ->line('Clé : ' . $this->serialKey->serial_key)
            ->line('Projet : ' . $this->serialKey->project->name)
            ->line('Domaine : ' . ($this->serialKey->domain ?? 'Non spécifié'))
            ->action('Voir les détails', route('admin.serial-keys.show', $this->serialKey))
            ->line('Date de modification : ' . now()->format('d/m/Y H:i'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'serial_key_id' => $this->serialKey->id,
            'action' => $this->action,
            'project_id' => $this->serialKey->project_id,
            'serial_key' => $this->serialKey->serial_key,
            'project_name' => $this->serialKey->project->name,
            'domain' => $this->serialKey->domain,
            'timestamp' => now()->format('d/m/Y H:i')
        ];
    }
    
    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        $statusMessages = [
            'revoked' => 'révoquée',
            'suspended' => 'suspendue',
            'expired' => 'expirée',
            'activated' => 'activée',
            'renewed' => 'renouvelée'
        ];

        $status = $statusMessages[$this->action] ?? $this->action;
        
        return [
            'id' => $this->id,
            'serial_key_id' => $this->serialKey->id,
            'serial_key' => $this->serialKey->serial_key,
            'action' => $this->action,
            'status_text' => $status,
            'project_id' => $this->serialKey->project_id,
            'project_name' => $this->serialKey->project->name,
            'domain' => $this->serialKey->domain,
            'timestamp' => now()->format('d/m/Y H:i'),
            'read' => false,
            'title' => 'Changement de statut de licence',
            'message' => 'La licence ' . $this->serialKey->serial_key . ' a été ' . $status
        ];
    }
}