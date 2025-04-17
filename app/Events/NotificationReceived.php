<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $recipient;

    /**
     * Create a new event instance.
     *
     * @param Notification $notification
     * @param array $recipient
     * @return void
     */
    public function __construct(Notification $notification, array $recipient)
    {
        $this->notification = $notification;
        $this->recipient = $recipient;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Déterminer le type de destinataire (admin ou user)
        $recipientType = isset($this->recipient['is_super_admin']) ? 'admin' : 'user';
        $recipientId = $this->recipient['id'];
        
        return new PrivateChannel("{$recipientType}.{$recipientId}");
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'type' => $this->notification->type,
            'metadata' => $this->notification->metadata,
            'created_at' => $this->notification->created_at->toIso8601String(),
        ];
    }
}
