<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Licence;

class LicenceExpiring extends Notification implements ShouldQueue
{
    use Queueable;

    protected Licence $licence;
    protected int $daysRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct(Licence $licence, int $daysRemaining)
    {
        $this->licence = $licence;
        $this->daysRemaining = $daysRemaining;
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
        $product = $this->licence->product;
        $expirationDate = $this->licence->expiration_date->format('d/m/Y');
        
        return (new MailMessage)
            ->subject('Votre licence expire bientôt - Action requise')
            ->greeting('Bonjour ' . $this->licence->client_name . ',')
            ->line('Nous vous informons que votre licence pour ' . ($product ? $product->name : 'notre produit') . ' expire bientôt.')
            ->line('**Détails de la licence :**')
            ->line('- **Clé de licence :** ' . $this->licence->licence_key)
            ->line('- **Produit :** ' . ($product ? $product->name . ' (v' . $product->version . ')' : 'Non spécifié'))
            ->line('- **Date d\'expiration :** ' . $expirationDate . ' (' . $this->daysRemaining . ' jours restants)')
            ->line('Pour continuer à utiliser ce produit sans interruption, veuillez renouveler votre licence avant sa date d\'expiration.')
            ->action('Renouveler ma licence', url('/dashboard/licences/' . $this->licence->id))
            ->line('Si vous avez des questions ou besoin d\'assistance, n\'hésitez pas à nous contacter.')
            ->line('Merci de votre confiance !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'licence_id' => $this->licence->id,
            'licence_key' => $this->licence->licence_key,
            'product_id' => $this->licence->product_id,
            'product_name' => $this->licence->product ? $this->licence->product->name : null,
            'client_name' => $this->licence->client_name,
            'client_email' => $this->licence->client_email,
            'expiration_date' => $this->licence->expiration_date->format('Y-m-d'),
            'days_remaining' => $this->daysRemaining
        ];
    }
}
