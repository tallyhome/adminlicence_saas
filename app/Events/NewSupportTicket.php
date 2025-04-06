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

class NewSupportTicket implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Le ticket de support concerné
     *
     * @var SupportTicket
     */
    public $ticket;

    /**
     * Données supplémentaires pour la notification
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
        
        $this->data = [
            'ticket_id' => $ticket->id,
            'subject' => $ticket->subject,
            'priority' => $ticket->priority,
            'status' => $ticket->status,
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
        return 'support.ticket.created';
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