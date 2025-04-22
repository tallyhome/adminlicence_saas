<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Licence;
use App\Notifications\LicenceExpiring;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckExpiringLicences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expiring-licences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les licences qui expirent bientôt et envoie des notifications aux clients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des licences qui expirent bientôt...');
        
        // Définir les seuils pour les notifications (en jours)
        $thresholds = [30, 14, 7, 3, 1];
        
        foreach ($thresholds as $days) {
            // Trouver les licences qui expirent exactement dans X jours
            $expirationDate = Carbon::now()->addDays($days)->startOfDay();
            $nextDay = Carbon::now()->addDays($days + 1)->startOfDay();
            
            $licences = Licence::where('status', 'active')
                ->whereNotNull('expiration_date')
                ->whereNotNull('client_email')
                ->where('expiration_date', '>=', $expirationDate)
                ->where('expiration_date', '<', $nextDay)
                ->get();
            
            $count = $licences->count();
            $this->info("Licences expirant dans {$days} jours : {$count}");
            
            if ($count > 0) {
                foreach ($licences as $licence) {
                    try {
                        // Envoyer la notification au client
                        Notification::route('mail', $licence->client_email)
                            ->notify(new LicenceExpiring($licence, $days));
                        
                        $this->info("Notification envoyée pour la licence #{$licence->id} ({$licence->licence_key}) à {$licence->client_email}");
                        
                        // Journaliser l'envoi de la notification
                        Log::info("Notification d'expiration envoyée", [
                            'licence_id' => $licence->id,
                            'licence_key' => $licence->licence_key,
                            'client_email' => $licence->client_email,
                            'days_remaining' => $days,
                            'expiration_date' => $licence->expiration_date->format('Y-m-d')
                        ]);
                    } catch (\Exception $e) {
                        $this->error("Erreur lors de l'envoi de la notification pour la licence #{$licence->id}: " . $e->getMessage());
                        
                        Log::error("Erreur lors de l'envoi de la notification d'expiration", [
                            'licence_id' => $licence->id,
                            'client_email' => $licence->client_email,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
        
        $this->info('Vérification terminée.');
        
        return Command::SUCCESS;
    }
}
