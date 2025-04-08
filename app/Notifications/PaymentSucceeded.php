<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSucceeded extends Notification implements ShouldQueue
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
        $message = (new MailMessage)
            ->subject('Paiement reçu - Facture #' . $this->invoice->number)
            ->greeting('Bonjour ' . $notifiable->name . ',');

        // Détails du paiement
        $message->line('Nous avons bien reçu votre paiement de ' . number_format($this->invoice->total, 2) . ' ' . $this->invoice->currency . '.')
            ->line('Détails de la facture :')
            ->line('- Numéro : ' . $this->invoice->number)
            ->line('- Date de paiement : ' . $this->invoice->paid_at->format('d/m/Y'))
            ->line('- Période de facturation : ' . date('d/m/Y', strtotime($this->invoice->period_start)) . ' au ' . date('d/m/Y', strtotime($this->invoice->period_end)))
            ->line('- Méthode de paiement : ' . ucfirst($this->invoice->payment_method_type));

        // Détails des éléments facturés
        if ($this->invoice->items->count() > 0) {
            $message->line('\nDétail des éléments facturés :');
            foreach ($this->invoice->items as $item) {
                $message->line('- ' . $item->description . ' : ' . number_format($item->amount, 2) . ' ' . $this->invoice->currency);
            }
        }

        // Lien vers la facture et message de remerciement
        $message->action('Voir la facture complète', route('invoices.show', $this->invoice->id))
            ->line('\nMerci de votre confiance !');

        return $message;
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