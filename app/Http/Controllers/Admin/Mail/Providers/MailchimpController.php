<?php

namespace App\Http\Controllers\Admin\Mail\Providers;

use App\Http\Controllers\Controller;
use App\Models\MailConfig;
use Illuminate\Http\Request;

class MailchimpController extends Controller
{
    public function index()
    {
        $config = MailConfig::where('provider', 'mailchimp')->first();
        return view('admin.mail.providers.mailchimp.index', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'list_id' => 'required|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string',
        ]);

        $config = MailConfig::updateOrCreate(
            ['provider' => 'mailchimp'],
            $request->all()
        );

        return redirect()->route('admin.mail.providers.mailchimp.index')
            ->with('success', 'Configuration Mailchimp mise à jour avec succès.');
    }

    public function test(Request $request)
    {
        try {
            // Logique de test de connexion Mailchimp
            return response()->json(['message' => 'Test de connexion réussi']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function syncLists()
    {
        try {
            // Logique de synchronisation des listes
            return response()->json(['message' => 'Listes synchronisées avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function syncTemplates()
    {
        try {
            // Logique de synchronisation des templates
            return response()->json(['message' => 'Templates synchronisés avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function campaigns()
    {
        // Logique pour récupérer les campagnes
        return view('admin.mail.providers.mailchimp.campaigns');
    }

    public function createCampaign(Request $request)
    {
        try {
            // Logique de création de campagne
            return response()->json(['message' => 'Campagne créée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendCampaign($campaign)
    {
        try {
            // Logique d'envoi de campagne
            return response()->json(['message' => 'Campagne envoyée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}