<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class InvoiceController extends Controller
{
    /**
     * Affiche la liste des factures
     */
    public function index(Request $request)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé. Vous devez être connecté en tant qu\'administrateur.');
        }
        
        $admin = Auth::guard('admin')->user();
        $userId = $request->input('user_id');
        
        // Filtrer par utilisateur si spécifié
        $query = Invoice::with('user');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else if (!$admin->is_super_admin) {
            // Admin normal voit uniquement les factures de ses utilisateurs
            $userIds = User::where('admin_id', $admin->id)->pluck('id');
            $query->whereIn('user_id', $userIds);
        }
        
        $invoices = $query->latest()->paginate(15);
        
        return view('admin.invoices.index', compact('invoices'));
    }
    
    /**
     * Affiche les détails d'une facture
     */
    public function show($id)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé. Vous devez être connecté en tant qu\'administrateur.');
        }
        
        $admin = Auth::guard('admin')->user();
        $invoice = Invoice::with('user')->findOrFail($id);
        
        // Vérifier que l'admin a le droit de voir cette facture
        if (!$admin->is_super_admin) {
            $userIds = User::where('admin_id', $admin->id)->pluck('id');
            if (!$userIds->contains($invoice->user_id)) {
                abort(403, 'Vous n\'avez pas accès à cette facture.');
            }
        }
        
        return view('admin.invoices.show', compact('invoice'));
    }
    
    /**
     * Télécharge une facture au format PDF
     */
    public function download($id)
    {
        // Vérifier si l'utilisateur est connecté en tant qu'admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Accès non autorisé. Vous devez être connecté en tant qu\'administrateur.');
        }
        
        $admin = Auth::guard('admin')->user();
        $invoice = Invoice::with('user')->findOrFail($id);
        
        // Vérifier que l'admin a le droit de voir cette facture
        if (!$admin->is_super_admin) {
            $userIds = User::where('admin_id', $admin->id)->pluck('id');
            if (!$userIds->contains($invoice->user_id)) {
                abort(403, 'Vous n\'avez pas accès à cette facture.');
            }
        }
        
        // Pour l'instant, on retourne une réponse simple
        // Dans une implémentation réelle, on générerait un PDF
        return Response::make('Facture PDF pour ' . $invoice->number, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="facture-' . $invoice->number . '.pdf"'
        ]);
    }
}
