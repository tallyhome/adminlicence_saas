<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var SupportTicket
     */
    protected $ticket;

    /**
     * @var string
     */
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(SupportTicket $ticket, string $status)
    {
        $this->ticket = $ticket;
        $this->status = $status;
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
            'open' => 'ouvert',
            'in_progress' => 'en cours',
            'waiting' => 'en attente',
            'closed' => 'fermé'
        ];
        
        $statusText = $statusMessages[$this->status] ?? $this->status;
        
        return (new MailMessage)
            ->subject('Mise à jour du ticket de support #' . $this->ticket->id)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le statut de votre ticket de support a été mis à jour.')
            ->line('Sujet: ' . $this->ticket->subject)
            ->line('Nouveau statut: ' . $statusText)
            ->action('Voir le ticket', url('/tickets/' . $this->ticket->id))
            ->line('Merci d\'utiliser notre plateforme!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusMessages = [
            'open' => 'ouvert',
            'in_progress' => 'en cours',
            'waiting' => 'en attente',
            'closed' => 'fermé'
        ];
        
        $statusText = $statusMessages[$this->status] ?? $this->status;
        
        return [
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'status' => $this->status,
            'status_text' => $statusText,
            'priority' => $this->ticket->priority,
            'timestamp' => now()->format('d/m/Y H:i')
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toBroadcast(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}