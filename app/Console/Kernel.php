<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Vérifier les licences qui expirent bientôt tous les jours à 8h du matin
        $schedule->command('app:check-expiring-licences')->dailyAt('08:00');
        
        // Envoyer le rapport hebdomadaire tous les lundis à 9h du matin
        $schedule->command('app:send-weekly-report')->weekly()->mondays()->at('09:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
