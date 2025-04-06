<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPayment implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * La facture concernée
     *
     * @var Invoice
     */
    public $invoice;

    /**
     * Données supplémentaires pour la notification
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        
        $this->data = [
            'invoice_id' => $invoice->id,
            'number' => $invoice->number,
            'total' => $invoice->total,
            'currency' => $invoice->currency,
            'tenant_id' => $invoice->tenant_id,
            'tenant_name' => $invoice->tenant->name ?? 'Client inconnu',
            'subscription_id' => $invoice->subscription_id,
            'payment_method' => $invoice->paymentMethod ? $invoice->paymentMethod->display_name : 'Méthode inconnue',
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
            new PrivateChannel('admin-dashboard'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'new.payment';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'invoice' => $this->data,
            'message' => 'Nouveau paiement reçu : ' . $this->data['total'] . ' ' . strtoupper($this->data['currency']),
            'type' => 'payment',
            'timestamp' => $this->data['timestamp']
        ];
    }
}