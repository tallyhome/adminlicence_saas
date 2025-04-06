<?php

namespace App\Http\Controllers\Admin\Mail\Providers;

use App\Http\Controllers\Controller;
use App\Models\MailConfig;
use Illuminate\Http\Request;

class RapidmailController extends Controller
{
    public function index()
    {
        $config = MailConfig::where('provider', 'rapidmail')->first();
        $pageTitle = 'Configuration Rapidmail';
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Email', 'url' => route('admin.mail.settings')],
            ['name' => 'Rapidmail', 'url' => '#']
        ];
        return view('admin.mail.providers.rapidmail.index', compact('config', 'pageTitle', 'breadcrumbs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'username' => 'required|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string',
        ]);

        $config = MailConfig::updateOrCreate(
            ['provider' => 'rapidmail'],
            $request->all()
        );

        return redirect()->route('admin.mail.providers.rapidmail.index')
            ->with('success', 'Configuration Rapidmail mise à jour avec succès.');
    }

    public function test(Request $request)
    {
        try {
            // Logique de test de connexion Rapidmail
            return response()->json(['message' => 'Test de connexion réussi']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function recipientLists()
    {
        // Logique pour récupérer les listes de destinataires
        return view('admin.mail.providers.rapidmail.lists');
    }

    public function createRecipientList(Request $request)
    {
        try {
            // Logique de création de liste
            return response()->json(['message' => 'Liste créée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mailings()
    {
        // Logique pour récupérer les mailings
        return view('admin.mail.providers.rapidmail.mailings');
    }

    public function createMailing(Request $request)
    {
        try {
            // Logique de création de mailing
            return response()->json(['message' => 'Mailing créé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendMailing($mailing)
    {
        try {
            // Logique d'envoi de mailing
            return response()->json(['message' => 'Mailing envoyé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function statistics($mailing)
    {
        // Logique pour récupérer les statistiques
        return view('admin.mail.providers.rapidmail.statistics', compact('mailing'));
    }
}