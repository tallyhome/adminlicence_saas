<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ExportController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exporte les licences de l'utilisateur au format CSV
     */
    public function exportLicences(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Récupérer les licences de l'utilisateur
            $query = $user->licences()->with('product');
            
            // Filtrer par statut si spécifié
            if ($request->has('is_active') && $request->is_active !== null) {
                $query->where('is_active', $request->is_active);
            }
            
            // Filtrer par produit si spécifié
            if ($request->has('product_id') && !empty($request->product_id)) {
                $query->where('product_id', $request->product_id);
            }
            
            $licences = $query->get();
            
            // Créer la réponse CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="licences_' . date('Y-m-d') . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];
            
            $callback = function() use ($licences) {
                $file = fopen('php://output', 'w');
                
                // En-têtes CSV
                fputcsv($file, [
                    'ID',
                    'Clé de licence',
                    'Produit',
                    'Version',
                    'Client',
                    'Email',
                    'Statut',
                    'Date d\'expiration',
                    'Activations',
                    'Max activations',
                    'Date de création',
                    'Dernière modification'
                ]);
                
                // Données
                foreach ($licences as $licence) {
                    fputcsv($file, [
                        $licence->id,
                        $licence->licence_key,
                        $licence->product ? $licence->product->name : 'N/A',
                        $licence->product ? $licence->product->version : 'N/A',
                        $licence->client_name,
                        $licence->client_email,
                        $licence->is_active ? 'Active' : 'Inactive',
                        $licence->expiration_date ? $licence->expiration_date->format('Y-m-d') : 'Illimitée',
                        $licence->activations()->count(),
                        $licence->max_activations ?: 'Illimité',
                        $licence->created_at->format('Y-m-d H:i:s'),
                        $licence->updated_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                fclose($file);
            };
            
            Log::info('Exportation des licences', [
                'user_id' => $user->id,
                'count' => $licences->count(),
                'filters' => $request->only(['is_active', 'product_id'])
            ]);
            
            return new StreamedResponse($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'exportation des licences', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de l\'exportation des licences: ' . $e->getMessage());
        }
    }
    
    /**
     * Exporte les produits de l'utilisateur au format CSV
     */
    public function exportProducts(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Récupérer les produits de l'utilisateur
            $query = $user->products();
            
            // Filtrer par statut si spécifié
            if ($request->has('is_active') && $request->is_active !== null) {
                $query->where('is_active', $request->is_active);
            }
            
            $products = $query->get();
            
            // Créer la réponse CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="produits_' . date('Y-m-d') . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];
            
            $callback = function() use ($products) {
                $file = fopen('php://output', 'w');
                
                // En-têtes CSV
                fputcsv($file, [
                    'ID',
                    'Nom',
                    'Version',
                    'Description',
                    'Prix',
                    'Statut',
                    'Nombre de licences',
                    'Date de création',
                    'Dernière modification'
                ]);
                
                // Données
                foreach ($products as $product) {
                    fputcsv($file, [
                        $product->id,
                        $product->name,
                        $product->version,
                        $product->description,
                        $product->price ? number_format($product->price, 2) . ' €' : 'N/A',
                        $product->is_active ? 'Active' : 'Inactive',
                        $product->licences()->count(),
                        $product->created_at->format('Y-m-d H:i:s'),
                        $product->updated_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                fclose($file);
            };
            
            Log::info('Exportation des produits', [
                'user_id' => $user->id,
                'count' => $products->count(),
                'filters' => $request->only(['is_active'])
            ]);
            
            return new StreamedResponse($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'exportation des produits', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de l\'exportation des produits: ' . $e->getMessage());
        }
    }
    
    /**
     * Exporte les projets de l'utilisateur au format CSV
     */
    public function exportProjects(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Récupérer les projets de l'utilisateur
            $query = $user->projects();
            
            // Filtrer par statut si spécifié
            if ($request->has('is_active') && $request->is_active !== null) {
                $query->where('is_active', $request->is_active);
            }
            
            $projects = $query->get();
            
            // Créer la réponse CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="projets_' . date('Y-m-d') . '.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];
            
            $callback = function() use ($projects) {
                $file = fopen('php://output', 'w');
                
                // En-têtes CSV
                fputcsv($file, [
                    'ID',
                    'Nom',
                    'Description',
                    'URL du site',
                    'Statut',
                    'Total des clés',
                    'Clés actives',
                    'Date de création',
                    'Dernière modification'
                ]);
                
                // Données
                foreach ($projects as $project) {
                    fputcsv($file, [
                        $project->id,
                        $project->name,
                        $project->description,
                        $project->website_url,
                        $project->is_active ? 'Active' : 'Inactive',
                        $project->totalKeysCount() ?? 0,
                        $project->activeKeysCount() ?? 0,
                        $project->created_at->format('Y-m-d H:i:s'),
                        $project->updated_at->format('Y-m-d H:i:s')
                    ]);
                }
                
                fclose($file);
            };
            
            Log::info('Exportation des projets', [
                'user_id' => $user->id,
                'count' => $projects->count(),
                'filters' => $request->only(['is_active'])
            ]);
            
            return new StreamedResponse($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'exportation des projets', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de l\'exportation des projets: ' . $e->getMessage());
        }
    }
}
