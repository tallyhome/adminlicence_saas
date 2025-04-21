<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Vérifie si l'utilisateur est un administrateur
     */
    private function checkAdmin()
    {
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé. Seuls les administrateurs peuvent accéder à cette page.');
        }
    }
    
    /**
     * Affiche la liste des produits
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->checkAdmin();
        
        $products = Product::when(!Auth::guard('admin')->user()->is_super_admin, function ($query) {
                return $query->where('admin_id', Auth::guard('admin')->id());
            })
            ->latest()
            ->paginate(10);
            
        return view('admin.products.index', compact('products'));
    }
    
    /**
     * Affiche le formulaire de création d'un produit
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->checkAdmin();
        
        return view('admin.products.create');
    }
    
    /**
     * Enregistre un nouveau produit
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->checkAdmin();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'max_activations_per_licence' => 'nullable|integer|min:1',
            'licence_duration_days' => 'nullable|integer|min:1',
        ]);
        
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'admin_id' => Auth::guard('admin')->id(),
            'version' => $request->version ?? '1.0',
            'is_active' => $request->has('is_active'),
            'max_activations_per_licence' => $request->max_activations_per_licence,
            'licence_duration_days' => $request->licence_duration_days,
        ]);
        
        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Produit créé avec succès.');
    }
    
    /**
     * Affiche les détails d'un produit
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
    {
        $this->checkAdmin();
        
        // Vérifier que l'admin a le droit de voir ce produit
        if (!Auth::guard('admin')->user()->is_super_admin && $product->admin_id !== Auth::guard('admin')->id()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de voir ce produit.');
        }
        
        $product->load('licences');
        
        return view('admin.products.show', compact('product'));
    }
    
    /**
     * Affiche le formulaire de modification d'un produit
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $this->checkAdmin();
        
        // Vérifier que l'admin a le droit de modifier ce produit
        if (!Auth::guard('admin')->user()->is_super_admin && $product->admin_id !== Auth::guard('admin')->id()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de modifier ce produit.');
        }
        
        return view('admin.products.edit', compact('product'));
    }
    
    /**
     * Met à jour un produit
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $this->checkAdmin();
        
        // Vérifier que l'admin a le droit de modifier ce produit
        if (!Auth::guard('admin')->user()->is_super_admin && $product->admin_id !== Auth::guard('admin')->id()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de modifier ce produit.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'max_activations_per_licence' => 'nullable|integer|min:1',
            'licence_duration_days' => 'nullable|integer|min:1',
        ]);
        
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'version' => $request->version,
            'is_active' => $request->has('is_active'),
            'max_activations_per_licence' => $request->max_activations_per_licence,
            'licence_duration_days' => $request->licence_duration_days,
        ]);
        
        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Produit mis à jour avec succès.');
    }
    
    /**
     * Supprime un produit
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        $this->checkAdmin();
        
        // Vérifier que l'admin a le droit de supprimer ce produit
        if (!Auth::guard('admin')->user()->is_super_admin && $product->admin_id !== Auth::guard('admin')->id()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de supprimer ce produit.');
        }
        
        // Vérifier si le produit a des licences associées
        if ($product->licences()->count() > 0) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Ce produit ne peut pas être supprimé car il a des licences associées.');
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}
