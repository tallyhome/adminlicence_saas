<?php

namespace App\Events;

use App\Models\SerialKey;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LicenceStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La clé de série concernée
     *
     * @var SerialKey
     */
    public $serialKey;

    /**
     * L'action effectuée sur la licence
     *
     * @var string
     */
    public $action;

    /**
     * Texte du statut formaté
     *
     * @var string
     */
    public $statusText;

    /**
     * Données supplémentaires pour la notification
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(SerialKey $serialKey, string $action)
    {
        $this->serialKey = $serialKey;
        $this->action = $action;
        
        $statusMessages = [
            'revoked' => 'révoquée',
            'suspended' => 'suspendue',
            'expired' => 'expirée',
            'activated' => 'activée',
            'renewed' => 'renouvelée'
        ];
        
        $this->statusText = $statusMessages[$action] ?? $action;
        
        $this->data = [
            'serial_key_id' => $serialKey->id,
            'serial_key' => $serialKey->serial_key,
            'action' => $action,
            'status_text' => $this->statusText,
            'project_id' => $serialKey->project_id,
            'project_name' => $serialKey->project->name,
            'domain' => $serialKey->domain,
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
            new Channel('licence-status'),
            new PrivateChannel('project.' . $this->serialKey->project_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'licence.status.changed';
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