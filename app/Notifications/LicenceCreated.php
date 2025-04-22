<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Licence;

class LicenceCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Licence $licence)
    {
        //
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
        $expirationText = $this->licence->expiration_date 
            ? 'Cette licence expire le ' . $this->licence->expiration_date->format('d/m/Y') 
            : 'Cette licence n\'a pas de date d\'expiration.';
        
        $activationsText = $this->licence->max_activations 
            ? 'Nombre maximum d\'activations : ' . $this->licence->max_activations 
            : 'Nombre illimité d\'activations.';

        return (new MailMessage)
            ->subject('Votre nouvelle licence pour ' . ($product ? $product->name : 'notre produit'))
            ->greeting('Bonjour ' . $this->licence->client_name . ',')
            ->line('Votre licence a été créée avec succès.')
            ->line('Voici les détails de votre licence :')
            ->line('**Clé de licence :** ' . $this->licence->licence_key)
            ->line('**Produit :** ' . ($product ? $product->name . ' (v' . $product->version . ')' : 'Non spécifié'))
            ->line($expirationText)
            ->line($activationsText)
            ->line('Vous pouvez utiliser cette clé pour activer votre produit.')
            ->action('Voir les détails de votre licence', url('/dashboard/licences/' . $this->licence->id))
            ->line('Merci d\'utiliser nos services !');
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
        ];
    }
}
