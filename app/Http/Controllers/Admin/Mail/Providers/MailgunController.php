<?php

namespace App\Http\Controllers\Admin\Mail\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class MailgunController extends Controller
{
    public function index()
    {
        $config = [
            'domain' => config('services.mailgun.domain'),
            'secret' => config('services.mailgun.secret'),
            'endpoint' => config('services.mailgun.endpoint', 'api.mailgun.net'),
            'scheme' => config('services.mailgun.scheme', 'https'),
        ];

        return view('admin.mail.providers.mailgun.index', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'domain' => 'required|string',
            'secret' => 'required|string',
            'endpoint' => 'required|string',
            'scheme' => 'required|string|in:http,https',
        ]);

        // Mettre à jour le fichier de configuration
        $this->updateConfig($request->all());

        return redirect()->back()->with('success', 'Configuration Mailgun mise à jour avec succès');
    }

    public function test(Request $request)
    {
        try {
            $mailgun = new \Mailgun\Mailgun(config('services.mailgun.secret'));
            $domain = config('services.mailgun.domain');

            // Tester la connexion en récupérant les informations du domaine
            $result = $mailgun->domains()->show($domain);

            return response()->json([
                'success' => true,
                'message' => 'Connexion à Mailgun établie avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion à Mailgun : ' . $e->getMessage()
            ], 400);
        }
    }

    public function logs()
    {
        try {
            $mailgun = new \Mailgun\Mailgun(config('services.mailgun.secret'));
            $domain = config('services.mailgun.domain');

            // Récupérer les logs des événements
            $events = $mailgun->events()->get($domain, [
                'limit' => 100,
                'ascending' => 'no'
            ]);

            return view('admin.mail.providers.mailgun.logs', [
                'events' => $events->getItems()
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la récupération des logs : ' . $e->getMessage());
        }
    }

    public function clearLogs()
    {
        return response()->json([
            'success' => true,
            'message' => 'Les logs ne peuvent pas être supprimés via l\'API Mailgun. Veuillez utiliser le tableau de bord Mailgun.'
        ]);
    }

    private function updateConfig(array $data)
    {
        $envFile = base_path('.env');
        $content = file_get_contents($envFile);

        $content = preg_replace(
            [
                '/^MAILGUN_DOMAIN=.*/m',
                '/^MAILGUN_SECRET=.*/m',
                '/^MAILGUN_ENDPOINT=.*/m',
                '/^MAILGUN_SCHEME=.*/m'
            ],
            [
                'MAILGUN_DOMAIN=' . $data['domain'],
                'MAILGUN_SECRET=' . $data['secret'],
                'MAILGUN_ENDPOINT=' . $data['endpoint'],
                'MAILGUN_SCHEME=' . $data['scheme']
            ],
            $content
        );

        file_put_contents($envFile, $content);

        // Mettre à jour la configuration en cours
        config([
            'services.mailgun.domain' => $data['domain'],
            'services.mailgun.secret' => $data['secret'],
            'services.mailgun.endpoint' => $data['endpoint'],
            'services.mailgun.scheme' => $data['scheme']
        ]);
    }
}