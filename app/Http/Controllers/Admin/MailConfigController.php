<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class MailConfigController extends Controller
{
    public function index()
    {
        $mailConfig = MailConfig::first() ?? new MailConfig();
        return view('admin.mail-settings', compact('mailConfig'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mailer' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'nullable|string',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
            'template_name' => 'nullable|string',
            'template_content' => 'nullable|string',
        ]);

        $mailConfig = MailConfig::first() ?? new MailConfig();
        $mailConfig->fill($validated);
        $mailConfig->save();

        // Mettre à jour la configuration du mailer
        Config::set('mail.mailers.smtp', $mailConfig->toMailConfig());
        Config::set('mail.from.address', $mailConfig->from_address);
        Config::set('mail.from.name', $mailConfig->from_name);

        return redirect()->route('admin.mail.settings')
            ->with('success', 'Configuration email mise à jour avec succès');
    }

    public function test(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $mailConfig = MailConfig::first();
            if (!$mailConfig) {
                return back()->with('error', 'Veuillez d\'abord configurer les paramètres SMTP');
            }

            // Configurer le mailer avec les paramètres de la base de données
            Config::set('mail.mailers.smtp', $mailConfig->toMailConfig());
            Config::set('mail.from.address', $mailConfig->from_address);
            Config::set('mail.from.name', $mailConfig->from_name);

            // Envoyer l'email de test
            Mail::raw('Ceci est un email de test depuis votre application.', function($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test de configuration email');
            });

            return back()->with('success', 'Email de test envoyé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }
}