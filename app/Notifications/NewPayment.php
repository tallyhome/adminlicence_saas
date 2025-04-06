<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPayment extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * La facture concernée
     *
     * @var Invoice
     */
    protected $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->invoice->total, 2) . ' ' . strtoupper($this->invoice->currency);
        $tenantName = $this->invoice->tenant->name ?? 'Client inconnu';
        
        return (new MailMessage)
            ->subject('Nouveau paiement reçu')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un nouveau paiement a été reçu.')
            ->line('Détails du paiement :')
            ->line('- Montant : ' . $amount)
            ->line('- Client : ' . $tenantName)
            ->line('- Numéro de facture : ' . $this->invoice->number)
            ->line('- Date : ' . $this->invoice->paid_at->format('d/m/Y H:i'))
            ->action('Voir la facture', url('/admin/invoices/' . $this->invoice->id))
            ->line('Merci d\'utiliser notre application !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $amount = number_format($this->invoice->total, 2) . ' ' . strtoupper($this->invoice->currency);
        $tenantName = $this->invoice->tenant->name ?? 'Client inconnu';
        
        return [
            'invoice_id' => $this->invoice->id,
            'number' => $this->invoice->number,
            'total' => $this->invoice->total,
            'currency' => $this->invoice->currency,
            'tenant_id' => $this->invoice->tenant_id,
            'tenant_name' => $tenantName,
            'subscription_id' => $this->invoice->subscription_id,
            'payment_method' => $this->invoice->paymentMethod ? $this->invoice->paymentMethod->display_name : 'Méthode inconnue',
            'message' => 'Nouveau paiement reçu : ' . $amount . ' de ' . $tenantName,
            'type' => 'payment',
            'timestamp' => now()->format('d/m/Y H:i')
        ];
    }
}