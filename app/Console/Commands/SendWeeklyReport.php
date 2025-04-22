<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\WeeklyReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weekly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie un rapport hebdomadaire aux utilisateurs avec un résumé de leurs licences, produits et projets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de l\'envoi des rapports hebdomadaires...');
        
        $users = User::whereHas('subscription', function($query) {
            $query->where('status', 'active');
        })->get();
        
        $count = 0;
        
        foreach ($users as $user) {
            try {
                // Récupérer les statistiques de l'utilisateur
                $stats = $this->getUserStats($user);
                
                // Envoyer la notification
                $user->notify(new WeeklyReport($stats));
                
                $count++;
                
                $this->info("Rapport envoyé à {$user->email}");
                
                // Log l'envoi du rapport
                Log::info('Rapport hebdomadaire envoyé', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'stats' => $stats
                ]);
                
                // Pause pour éviter de surcharger le serveur de mail
                sleep(1);
                
            } catch (\Exception $e) {
                $this->error("Erreur lors de l'envoi du rapport à {$user->email}: {$e->getMessage()}");
                
                Log::error('Erreur lors de l\'envoi du rapport hebdomadaire', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("Envoi des rapports terminé. {$count} rapports envoyés sur {$users->count()} utilisateurs.");
        
        return Command::SUCCESS;
    }
    
    /**
     * Récupère les statistiques d'un utilisateur
     */
    private function getUserStats(User $user): array
    {
        // Projets
        $projects = $user->projects;
        $activeProjects = $projects->where('status', 'active')->count();
        
        // Produits
        $products = $user->products;
        $activeProducts = $products->where('status', 'active')->count();
        
        // Licences
        $licences = $user->licences;
        $activeLicences = $licences->where('status', 'active')->count();
        $expiringLicences = $licences->where('status', 'active')
            ->filter(function ($licence) {
                return $licence->expiration_date && $licence->expiration_date->diffInDays(now()) <= 30;
            })->count();
        
        // Activations récentes (7 derniers jours)
        $recentActivations = 0;
        foreach ($licences as $licence) {
            $recentActivations += $licence->activations()
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
        }
        
        // Abonnement
        $subscription = $user->subscription;
        $subscriptionName = $subscription ? $subscription->plan->name : 'Aucun';
        $subscriptionStatus = $subscription ? $subscription->status : 'inactive';
        $subscriptionExpiration = $subscription && $subscription->ends_at 
            ? $subscription->ends_at->format('d/m/Y') 
            : 'N/A';
        
        // Utilisation des ressources
        $projectsLimit = $subscription ? $subscription->plan->projects_limit : 0;
        $productsLimit = $subscription ? $subscription->plan->products_limit : 0;
        $licencesLimit = $subscription ? $subscription->plan->licences_limit : 0;
        
        $projectsUsage = $projectsLimit > 0 ? round(($projects->count() / $projectsLimit) * 100) : 0;
        $productsUsage = $productsLimit > 0 ? round(($products->count() / $productsLimit) * 100) : 0;
        $licencesUsage = $licencesLimit > 0 ? round(($licences->count() / $licencesLimit) * 100) : 0;
        
        return [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'subscription' => [
                'name' => $subscriptionName,
                'status' => $subscriptionStatus,
                'expiration' => $subscriptionExpiration,
            ],
            'projects' => [
                'total' => $projects->count(),
                'active' => $activeProjects,
                'usage' => $projectsUsage,
                'limit' => $projectsLimit,
            ],
            'products' => [
                'total' => $products->count(),
                'active' => $activeProducts,
                'usage' => $productsUsage,
                'limit' => $productsLimit,
            ],
            'licences' => [
                'total' => $licences->count(),
                'active' => $activeLicences,
                'expiring_soon' => $expiringLicences,
                'usage' => $licencesUsage,
                'limit' => $licencesLimit,
            ],
            'activations' => [
                'recent' => $recentActivations,
            ],
            'period' => [
                'start' => now()->subDays(7)->format('d/m/Y'),
                'end' => now()->format('d/m/Y'),
            ],
        ];
    }
}
