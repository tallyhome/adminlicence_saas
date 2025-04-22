<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyReport extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Les statistiques de l'utilisateur
     */
    protected array $stats;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $stats)
    {
        $this->stats = $stats;
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
        $stats = $this->stats;
        
        $message = (new MailMessage)
            ->subject('Votre rapport hebdomadaire - ' . config('app.name'))
            ->greeting('Bonjour ' . $stats['user']['name'] . ',')
            ->line('Voici votre rapport hebdomadaire du ' . $stats['period']['start'] . ' au ' . $stats['period']['end'] . '.')
            ->line('');
            
        // Section Abonnement
        $message->line('**Votre abonnement**')
            ->line('Plan: ' . $stats['subscription']['name'])
            ->line('Statut: ' . ($stats['subscription']['status'] === 'active' ? 'Actif' : 'Inactif'))
            ->line('Expiration: ' . $stats['subscription']['expiration'])
            ->line('');
            
        // Section Projets
        $message->line('**Projets**')
            ->line('Total: ' . $stats['projects']['total'] . ' projets')
            ->line('Actifs: ' . $stats['projects']['active'] . ' projets')
            ->line('Utilisation: ' . $stats['projects']['usage'] . '% (' . $stats['projects']['total'] . '/' . $stats['projects']['limit'] . ')')
            ->line('');
            
        // Section Produits
        $message->line('**Produits**')
            ->line('Total: ' . $stats['products']['total'] . ' produits')
            ->line('Actifs: ' . $stats['products']['active'] . ' produits')
            ->line('Utilisation: ' . $stats['products']['usage'] . '% (' . $stats['products']['total'] . '/' . $stats['products']['limit'] . ')')
            ->line('');
            
        // Section Licences
        $message->line('**Licences**')
            ->line('Total: ' . $stats['licences']['total'] . ' licences')
            ->line('Actives: ' . $stats['licences']['active'] . ' licences')
            ->line('Expirant bientôt: ' . $stats['licences']['expiring_soon'] . ' licences')
            ->line('Utilisation: ' . $stats['licences']['usage'] . '% (' . $stats['licences']['total'] . '/' . $stats['licences']['limit'] . ')')
            ->line('');
            
        // Section Activations
        $message->line('**Activations récentes**')
            ->line('Nouvelles activations cette semaine: ' . $stats['activations']['recent'])
            ->line('');
            
        // Pied de page
        $message->action('Accéder à mon tableau de bord', route('user.dashboard'))
            ->line('Merci d\'utiliser notre plateforme!');
            
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->stats;
    }
}
