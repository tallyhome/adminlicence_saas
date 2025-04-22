<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ProductController extends \Illuminate\Routing\Controller
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
     * Affiche la liste des produits de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        $products = $user->products()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('user.products.index', compact('products'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau produit.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Vérification des limites d'abonnement
        $subscription = $user->subscription;
        if ($subscription) {
            $plan = \App\Models\Plan::find($subscription->plan_id);
            if ($plan && $plan->max_products > 0) {
                $currentCount = $user->products()->count();
                if ($currentCount >= $plan->max_products) {
                    return redirect()->route('user.dashboard')
                        ->with('error', "Vous avez atteint la limite de {$plan->max_products} produits pour votre abonnement actuel. Veuillez mettre à niveau votre abonnement pour en créer davantage.");
                }
            }
        }
        
        return view('user.products.create');
    }

    /**
     * Enregistre un nouveau produit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'required|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'download_url' => 'nullable|url|max:255',
        ]);

        $user = Auth::user();
        
        // Vérification des limites d'abonnement
        $subscription = $user->subscription;
        if ($subscription) {
            $plan = \App\Models\Plan::find($subscription->plan_id);
            if ($plan && $plan->max_products > 0) {
                $currentCount = $user->products()->count();
                if ($currentCount >= $plan->max_products) {
                    return redirect()->route('user.dashboard')
                        ->with('error', "Vous avez atteint la limite de {$plan->max_products} produits pour votre abonnement actuel. Veuillez mettre à niveau votre abonnement pour en créer davantage.");
                }
            }
        }

        try {
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->version = $request->version;
            $product->price = $request->price;
            $product->download_url = $request->download_url;
            $product->is_active = 1;
            $product->user_id = $user->id;
            
            // Traitement de l'image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = Str::slug($request->name) . '-' . time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/products', $imageName);
                $product->image = 'products/' . $imageName;
            }
            
            $product->save();
            
            Log::info('Produit créé avec succès', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_name' => $product->name
            ]);
            
            return redirect()->route('user.products.show', $product->id)
                ->with('success', 'Produit créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du produit', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création du produit.');
        }
    }

    /**
     * Affiche les détails d'un produit.
     */
    public function show($id)
    {
        $user = Auth::user();
        $product = $user->products()->findOrFail($id);
        
        // Récupérer les statistiques du produit
        $totalLicences = $product->licences()->count();
        $activeLicences = $product->licences()->where('is_active', true)->count();
        
        return view('user.products.show', compact('product', 'totalLicences', 'activeLicences'));
    }

    /**
     * Affiche le formulaire d'édition d'un produit.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $product = $user->products()->findOrFail($id);
        
        return view('user.products.edit', compact('product'));
    }

    /**
     * Met à jour un produit.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'required|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'download_url' => 'nullable|url|max:255',
            'is_active' => 'required|boolean',
        ]);

        $user = Auth::user();
        $product = $user->products()->findOrFail($id);
        
        try {
            $product->name = $request->name;
            $product->description = $request->description;
            $product->version = $request->version;
            $product->price = $request->price;
            $product->download_url = $request->download_url;
            $product->is_active = $request->is_active ? 1 : 0;
            
            // Traitement de l'image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                if ($product->image) {
                    Storage::delete('public/' . $product->image);
                }
                
                $image = $request->file('image');
                $imageName = Str::slug($request->name) . '-' . time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/products', $imageName);
                $product->image = 'products/' . $imageName;
            }
            
            $product->save();
            
            Log::info('Produit mis à jour avec succès', [
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);
            
            return redirect()->route('user.products.show', $product->id)
                ->with('success', 'Produit mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du produit', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du produit.');
        }
    }

    /**
     * Supprime un produit.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $product = $user->products()->findOrFail($id);
        
        try {
            // Vérifier si le produit a des licences actives
            if ($product->licences()->where('is_active', true)->exists()) {
                return back()->with('error', 'Impossible de supprimer ce produit car il contient des licences actives.');
            }
            
            // Supprimer l'image si elle existe
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }
            
            $product->delete();
            
            Log::info('Produit supprimé avec succès', [
                'user_id' => $user->id,
                'product_id' => $id
            ]);
            
            return redirect()->route('user.products.index')
                ->with('success', 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du produit', [
                'user_id' => $user->id,
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de la suppression du produit.');
        }
    }
    
    /**
     * Télécharge le produit.
     */
    public function download($id)
    {
        $user = Auth::user();
        $product = $user->products()->findOrFail($id);
        
        if (!$product->download_url) {
            return back()->with('error', 'Aucun fichier de téléchargement disponible pour ce produit.');
        }
        
        Log::info('Téléchargement du produit', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
        
        return redirect()->away($product->download_url);
    }
}
