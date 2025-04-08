<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('Échec du paiement - ' . config('app.name'))
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous n\'avons pas pu traiter votre paiement de ' . number_format($this->invoice->total, 2) . ' ' . $this->invoice->currency . '.')
            ->line('Numéro de facture : ' . $this->invoice->number)
            ->line('Date de la tentative : ' . now()->format('d/m/Y'))
            ->line('Raison possible : Fonds insuffisants ou carte expirée')
            ->action('Mettre à jour le mode de paiement', route('billing.payment-methods.index'))
            ->line('Veuillez mettre à jour vos informations de paiement pour éviter toute interruption de service.')
            ->line('Si vous avez des questions, n\'hésitez pas à contacter notre support.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'amount' => $this->invoice->total,
            'currency' => $this->invoice->currency,
            'payment_method' => $this->invoice->payment_method_type,
        ];
    }
}