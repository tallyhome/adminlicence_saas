<?php

namespace App\Events;

use App\Models\SupportTicket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportTicketStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Le ticket de support concerné
     *
     * @var SupportTicket
     */
    public $ticket;

    /**
     * Le nouveau statut du ticket
     *
     * @var string
     */
    public $status;

    /**
     * Données supplémentaires pour la notification
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(SupportTicket $ticket, string $status)
    {
        $this->ticket = $ticket;
        $this->status = $status;
        
        $statusMessages = [
            'open' => 'ouvert',
            'in_progress' => 'en cours',
            'waiting' => 'en attente',
            'closed' => 'fermé'
        ];
        
        $statusText = $statusMessages[$status] ?? $status;
        
        $this->data = [
            'ticket_id' => $ticket->id,
            'subject' => $ticket->subject,
            'status' => $status,
            'status_text' => $statusText,
            'priority' => $ticket->priority,
            'client_id' => $ticket->client_id,
            'client_name' => $ticket->client->name ?? 'Client inconnu',
            'timestamp' => now()->format('d/m/Y H:i')
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('support-tickets'),
            new PrivateChannel('client.' . $this->ticket->client_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'support.ticket.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return $this->data;
    }
}