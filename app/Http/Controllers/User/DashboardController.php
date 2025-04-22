<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Project;
use App\Models\Product;
use App\Models\Licence;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class DashboardController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Affiche le tableau de bord de l'utilisateur
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $subscription = $user->subscription;
        $plan = null;
        
        if ($subscription) {
            $plan = Plan::find($subscription->plan_id);
        }
        
        // Récupérer les projets récents
        $projects = $user->projects()
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
        
        // Récupérer les produits récents
        $products = $user->products()
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
        
        // Récupérer les licences projet récentes
        $licences = $user->licences()
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
        
        // Récupérer les licences produit récentes si la relation existe
        $productLicences = collect();
        if (method_exists($user, 'productLicenses')) {
            $productLicences = $user->productLicenses()
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get();
        }
        
        // Récupérer les APIs récentes si la relation existe
        $apis = collect();
        if (method_exists($user, 'apis')) {
            $apis = $user->apis()
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
        }
        
        // Récupérer les clés d'API récentes si la relation existe
        $apiKeys = collect();
        if (method_exists($user, 'apiKeys')) {
            $apiKeys = $user->apiKeys()
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
        }
        
        return view('user.dashboard', compact(
            'user',
            'subscription',
            'plan',
            'projects',
            'products',
            'licences',
            'productLicences',
            'apis',
            'apiKeys'
        ));
    }
}
