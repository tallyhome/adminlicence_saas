<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Licence;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\LicenceCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class LicenceController extends \Illuminate\Routing\Controller
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
     * Affiche la liste des licences de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        $licences = $user->licences()->with('product')->orderBy('created_at', 'desc')->paginate(10);
        
        return view('user.licences.index', compact('licences'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle licence.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Vérification des limites d'abonnement
        $subscription = $user->subscription;
        if ($subscription) {
            $plan = \App\Models\Plan::find($subscription->plan_id);
            if ($plan && $plan->max_licences > 0) {
                $currentCount = $user->licences()->count();
                if ($currentCount >= $plan->max_licences) {
                    return redirect()->route('user.dashboard')
                        ->with('error', "Vous avez atteint la limite de {$plan->max_licences} licences pour votre abonnement actuel. Veuillez mettre à niveau votre abonnement pour en créer davantage.");
                }
            }
        }
        
        $products = $user->products()->where('is_active', true)->orWhere('is_active', 1)->get();
        
        if ($products->isEmpty()) {
            return redirect()->route('user.products.create')
                ->with('info', 'Vous devez d\'abord créer un produit avant de pouvoir générer des licences.');
        }
        
        return view('user.licences.create', compact('products'));
    }

    /**
     * Enregistre une nouvelle licence.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'expiration_date' => 'nullable|date|after:today',
            'max_activations' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        // Vérification des limites d'abonnement
        $subscription = $user->subscription;
        if ($subscription) {
            $plan = \App\Models\Plan::find($subscription->plan_id);
            if ($plan && $plan->max_licences > 0) {
                $currentCount = $user->licences()->count();
                if ($currentCount >= $plan->max_licences) {
                    return redirect()->route('user.dashboard')
                        ->with('error', "Vous avez atteint la limite de {$plan->max_licences} licences pour votre abonnement actuel. Veuillez mettre à niveau votre abonnement pour en créer davantage.");
                }
            }
        }
        
        // Vérifier que le produit appartient à l'utilisateur
        $product = $user->products()->findOrFail($request->product_id);
        
        try {
            $licence = new Licence();
            $licence->product_id = $product->id;
            $licence->user_id = $user->id;
            $licence->client_name = $request->client_name;
            $licence->client_email = $request->client_email;
            $licence->licence_key = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
            $licence->is_active = true;
            $licence->expiration_date = $request->expiration_date;
            $licence->max_activations = $request->max_activations;
            $licence->notes = $request->notes;
            $licence->save();
            
            Log::info('Licence créée avec succès', [
                'user_id' => $user->id,
                'licence_id' => $licence->id,
                'product_id' => $product->id
            ]);
            
            // Envoyer un email au client avec la clé de licence
            try {
                Notification::route('mail', $licence->client_email)
                    ->notify(new LicenceCreated($licence));
                
                Log::info('Email de licence envoyé avec succès', [
                    'user_id' => $user->id,
                    'licence_id' => $licence->id,
                    'client_email' => $licence->client_email
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de l\'email de licence', [
                    'user_id' => $user->id,
                    'licence_id' => $licence->id,
                    'error' => $e->getMessage()
                ]);
                // Continuer même si l'envoi d'email échoue
            }
            
            return redirect()->route('user.licences.show', $licence->id)
                ->with('success', 'Licence créée avec succès. Un email a été envoyé au client.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la licence', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la licence.');
        }
    }

    /**
     * Affiche les détails d'une licence.
     */
    public function show($id)
    {
        $user = Auth::user();
        $licence = $user->licences()->with('product')->findOrFail($id);
        
        // Récupérer les activations de la licence
        $activations = $licence->activations()->orderBy('created_at', 'desc')->get();
        
        return view('user.licences.show', compact('licence', 'activations'));
    }

    /**
     * Affiche le formulaire d'édition d'une licence.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $licence = $user->licences()->findOrFail($id);
        $products = $user->products()->where('is_active', true)->orWhere('is_active', 1)->get();
        
        return view('user.licences.edit', compact('licence', 'products'));
    }

    /**
     * Met à jour une licence.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'expiration_date' => 'nullable|date',
            'max_activations' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'is_active' => 'required|in:true,false',
        ]);

        $user = Auth::user();
        $licence = $user->licences()->findOrFail($id);
        
        // Vérifier que le produit appartient à l'utilisateur
        $product = $user->products()->findOrFail($request->product_id);
        
        try {
            $licence->product_id = $product->id;
            $licence->client_name = $request->client_name;
            $licence->client_email = $request->client_email;
            $licence->is_active = $request->is_active;
            $licence->expiration_date = $request->expiration_date;
            $licence->max_activations = $request->max_activations;
            $licence->notes = $request->notes;
            $licence->save();
            
            Log::info('Licence mise à jour avec succès', [
                'user_id' => $user->id,
                'licence_id' => $licence->id
            ]);
            
            return redirect()->route('user.licences.show', $licence->id)
                ->with('success', 'Licence mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la licence', [
                'user_id' => $user->id,
                'licence_id' => $licence->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de la licence.');
        }
    }

    /**
     * Supprime une licence.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $licence = $user->licences()->findOrFail($id);
        
        try {
            // Vérifier si la licence a des activations
            if ($licence->activations()->exists()) {
                // Ne pas supprimer la licence, mais la désactiver
                $licence->is_active = false;
                $licence->save();
                
                Log::info('Licence désactivée car elle a des activations', [
                    'user_id' => $user->id,
                    'licence_id' => $id
                ]);
                
                return redirect()->route('user.licences.index')
                    ->with('info', 'La licence a été désactivée car elle a des activations. Elle n\'a pas été supprimée.');
            }
            
            $licence->delete();
            
            Log::info('Licence supprimée avec succès', [
                'user_id' => $user->id,
                'licence_id' => $id
            ]);
            
            return redirect()->route('user.licences.index')
                ->with('success', 'Licence supprimée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la licence', [
                'user_id' => $user->id,
                'licence_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de la suppression de la licence.');
        }
    }
    
    /**
     * Régénère la clé de licence.
     */
    public function regenerateKey($id)
    {
        $user = Auth::user();
        $licence = $user->licences()->findOrFail($id);
        
        try {
            $oldKey = $licence->licence_key;
            $licence->licence_key = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
            $licence->save();
            
            Log::info('Clé de licence régénérée avec succès', [
                'user_id' => $user->id,
                'licence_id' => $licence->id,
                'old_key' => $oldKey,
                'new_key' => $licence->licence_key
            ]);
            
            return redirect()->route('user.licences.show', $licence->id)
                ->with('success', 'Clé de licence régénérée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la régénération de la clé de licence', [
                'user_id' => $user->id,
                'licence_id' => $licence->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de la régénération de la clé de licence.');
        }
    }
    
    /**
     * Envoie la clé de licence par email au client.
     */
    public function sendByEmail($id)
    {
        $user = Auth::user();
        $licence = $user->licences()->with('product')->findOrFail($id);
        
        try {
            // Envoyer la notification
            Notification::route('mail', $licence->client_email)
                ->notify(new LicenceCreated($licence));
            
            Log::info('Email de licence envoyé avec succès', [
                'user_id' => $user->id,
                'licence_id' => $licence->id,
                'client_email' => $licence->client_email
            ]);
            
            return redirect()->route('user.licences.show', $licence->id)
                ->with('success', 'Email envoyé avec succès à ' . $licence->client_email);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de licence', [
                'user_id' => $user->id,
                'licence_id' => $licence->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de l\'envoi de l\'email.');
        }
    }
}
