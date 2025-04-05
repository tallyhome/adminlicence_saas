<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class MailController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Afficher la page de configuration des emails
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = $this->mailService->getSettings();
        return view('admin.mail.settings', compact('settings'));
    }

    /**
     * Enregistrer les paramètres de messagerie
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'required|string|in:smtp,mailgun,ses,postmark,log,array',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|string|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        $this->mailService->updateSettings($validated);

        return redirect()->route('admin.mail.settings')
            ->with('success', 'Les paramètres de messagerie ont été mis à jour avec succès.');
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            Mail::raw('Ceci est un test d\'envoi d\'email depuis l\'interface d\'administration.', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Test d\'envoi d\'email');
            });

            return redirect()->route('admin.mail.settings')
                ->with('success', 'Email de test envoyé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('admin.mail.settings')
                ->with('error', 'Erreur lors de l\'envoi de l\'email de test: ' . $e->getMessage());
        }
    }
} 