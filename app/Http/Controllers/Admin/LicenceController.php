<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Licence;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenceController extends Controller
{
    /**
     * Affiche la liste des licences
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $licences = Licence::with(['user', 'product'])
            ->when(!auth('admin')->user()->is_super_admin, function ($query) {
                return $query->whereHas('product', function ($q) {
                    $q->where('admin_id', auth('admin')->id());
                });
            })
            ->latest()
            ->paginate(15);

        return view('admin.licences.index', compact('licences'));
    }

    /**
     * Affiche le formulaire de création d'une licence
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $products = Product::when(!auth('admin')->user()->is_super_admin, function ($query) {
                return $query->where('admin_id', auth('admin')->id());
            })
            ->where('is_active', true)
            ->get();
            
        $users = User::when(!auth('admin')->user()->is_super_admin, function ($query) {
                return $query->where('admin_id', auth('admin')->id());
            })
            ->get();

        return view('admin.licences.create', compact('products', 'users'));
    }

    /**
     * Enregistre une nouvelle licence
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'expires_at' => 'nullable|date|after:today',
            'max_activations' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::findOrFail($request->product_id);
        
        // Vérifier que l'admin a le droit de créer une licence pour ce produit
        if (!auth('admin')->user()->is_super_admin && $product->admin_id !== auth('admin')->id()) {
            return redirect()->route('admin.licences.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de créer une licence pour ce produit.');
        }

        // Créer la licence
        $licence = Licence::create([
            'licence_key' => Licence::generateLicenceKey(),
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'status' => Licence::STATUS_ACTIVE,
            'expires_at' => $request->expires_at,
            'max_activations' => $request->max_activations ?? $product->max_activations_per_licence,
            'current_activations' => 0,
        ]);

        return redirect()->route('admin.licences.show', $licence)
            ->with('success', 'Licence créée avec succès.');
    }

    /**
     * Affiche les détails d'une licence
     *
     * @param  \App\Models\Licence  $licence
     * @return \Illuminate\View\View
     */
    public function show(Licence $licence)
    {
        // Vérifier que l'admin a le droit de voir cette licence
        if (!auth('admin')->user()->is_super_admin && $licence->product->admin_id !== auth('admin')->id()) {
            return redirect()->route('admin.licences.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de voir cette licence.');
        }

        $licence->load(['user', 'product', 'activations']);

        return view('admin.licences.show', compact('licence'));
    }

    /**
     * Affiche le formulaire de modification d'une licence
     *
     * @param  \App\Models\Licence  $licence
     * @return \Illuminate\View\View
     */
    public function edit(Licence $licence)
    {
        // Vérifier que l'admin a le droit de modifier cette licence
        if (!auth('admin')->user()->is_super_admin && $licence->product->admin_id !== auth('admin')->id()) {
            return redirect()->route('admin.licences.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de modifier cette licence.');
        }

        $licence->load(['user', 'product']);

        return view('admin.licences.edit', compact('licence'));
    }

    /**
     * Met à jour une licence
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Licence  $licence
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Licence $licence)
    {
        // Vérifier que l'admin a le droit de modifier cette licence
        if (!auth('admin')->user()->is_super_admin && $licence->product->admin_id !== auth('admin')->id()) {
            return redirect()->route('admin.licences.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de modifier cette licence.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,expired,suspended,revoked',
            'expires_at' => 'nullable|date',
            'max_activations' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $licence->update([
            'status' => $request->status,
            'expires_at' => $request->expires_at,
            'max_activations' => $request->max_activations,
        ]);

        return redirect()->route('admin.licences.show', $licence)
            ->with('success', 'Licence mise à jour avec succès.');
    }

    /**
     * Supprime une licence
     *
     * @param  \App\Models\Licence  $licence
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Licence $licence)
    {
        // Vérifier que l'admin a le droit de supprimer cette licence
        if (!auth('admin')->user()->is_super_admin && $licence->product->admin_id !== auth('admin')->id()) {
            return redirect()->route('admin.licences.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de supprimer cette licence.');
        }

        // Supprimer d'abord les activations liées à cette licence
        $licence->activations()->delete();
        
        // Puis supprimer la licence
        $licence->delete();

        return redirect()->route('admin.licences.index')
            ->with('success', 'Licence supprimée avec succès.');
    }

    /**
     * Révoque une licence
     *
     * @param  \App\Models\Licence  $licence
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke(Licence $licence)
    {
        // Vérifier que l'admin a le droit de révoquer cette licence
        if (!auth('admin')->user()->is_super_admin && $licence->product->admin_id !== auth('admin')->id()) {
            return redirect()->route('admin.licences.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de révoquer cette licence.');
        }

        $licence->update([
            'status' => Licence::STATUS_REVOKED
        ]);

        return redirect()->route('admin.licences.show', $licence)
            ->with('success', 'Licence révoquée avec succès.');
    }

    /**
     * Régénère une clé de licence
     *
     * @param  \App\Models\Licence  $licence
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerateKey(Licence $licence)
    {
        // Vérifier que l'admin a le droit de régénérer cette licence
        if (!auth('admin')->user()->is_super_admin && $licence->product->admin_id !== auth('admin')->id()) {
            return redirect()->route('admin.licences.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de régénérer cette licence.');
        }

        $licence->update([
            'licence_key' => Licence::generateLicenceKey()
        ]);

        return redirect()->route('admin.licences.show', $licence)
            ->with('success', 'Clé de licence régénérée avec succès.');
    }
}
