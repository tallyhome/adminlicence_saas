<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $resourceType = null): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('user.login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();
        $subscription = $user->subscription;

        // Vérifier si l'utilisateur a un abonnement actif
        if (!$subscription) {
            Log::info('Utilisateur sans abonnement actif', ['user_id' => $user->id]);
            return redirect()->route('subscription.plans')
                ->with('error', 'Vous devez avoir un abonnement actif pour accéder à cette fonctionnalité.');
        }

        // Récupérer le plan associé à l'abonnement
        $plan = \App\Models\Plan::find($subscription->plan_id);
        
        if (!$plan) {
            Log::error('Plan d\'abonnement introuvable', [
                'user_id' => $user->id, 
                'subscription_id' => $subscription->id,
                'plan_id' => $subscription->plan_id
            ]);
            return redirect()->route('subscription.plans')
                ->with('error', 'Votre plan d\'abonnement est invalide. Veuillez contacter le support.');
        }

        // Vérifier les limites en fonction du type de ressource
        if ($resourceType) {
            switch ($resourceType) {
                case 'projects':
                    $currentCount = $user->projects()->count();
                    $maxAllowed = $plan->max_projects ?? 0;
                    $resourceName = 'projets';
                    break;
                    
                case 'project_licenses':
                    $currentCount = $user->licences()->count();
                    $maxAllowed = $plan->max_licenses ?? 0;
                    $resourceName = 'clés de licence projet';
                    break;
                    
                case 'products':
                    $currentCount = $user->products()->count();
                    $maxAllowed = $plan->max_products ?? 0;
                    $resourceName = 'produits';
                    break;
                
                case 'product_licenses':
                    $currentCount = $user->productLicenses()->count();
                    $maxAllowed = $plan->max_product_licenses ?? 0;
                    $resourceName = 'licences produit';
                    break;
                
                case 'apis':
                    // Vérifier d'abord si l'utilisateur a accès aux API
                    if (!$plan->has_api_access) {
                        return redirect()->route('dashboard')
                            ->with('error', "Votre abonnement actuel ne permet pas l'accès aux API. Veuillez mettre à niveau votre abonnement.");
                    }
                    
                    $currentCount = $user->apis()->count();
                    $maxAllowed = $plan->max_apis ?? 0;
                    $resourceName = 'APIs';
                    break;
                
                case 'api_keys':
                    // Vérifier d'abord si l'utilisateur a accès aux API
                    if (!$plan->has_api_access) {
                        return redirect()->route('dashboard')
                            ->with('error', "Votre abonnement actuel ne permet pas l'accès aux API. Veuillez mettre à niveau votre abonnement.");
                    }
                    
                    $currentCount = $user->apiKeys()->count();
                    $maxAllowed = $plan->max_api_keys ?? 0;
                    $resourceName = 'clés d\'API';
                    break;
                    
                default:
                    return $next($request);
            }
            
            // Si l'utilisateur a atteint la limite, le rediriger avec un message d'erreur
            // Note: maxAllowed = 0 signifie illimité
            if ($maxAllowed > 0 && $currentCount >= $maxAllowed) {
                Log::info('Limite d\'abonnement atteinte', [
                    'user_id' => $user->id,
                    'resource_type' => $resourceType,
                    'current_count' => $currentCount,
                    'max_allowed' => $maxAllowed
                ]);
                
                return redirect()->route('dashboard')
                    ->with('error', "Vous avez atteint la limite de {$maxAllowed} {$resourceName} pour votre abonnement actuel. Veuillez mettre à niveau votre abonnement pour en créer davantage.");
            }
        }

        return $next($request);
    }
}
