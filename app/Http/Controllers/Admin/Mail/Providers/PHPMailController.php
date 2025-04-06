<?php

namespace App\Http\Controllers\Admin\Mail\Providers;

use App\Http\Controllers\Controller;
use App\Models\MailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PHPMailController extends Controller
{
    public function index()
    {
        $config = MailConfig::where('provider', 'phpmail')->first();
        return view('admin.mail.providers.phpmail.index', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'required|string|in:tls,ssl',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
        ]);

        $config = MailConfig::updateOrCreate(
            ['provider' => 'phpmail'],
            $request->all()
        );

        return redirect()->route('admin.mail.providers.phpmail.index')
            ->with('success', 'Configuration PHPMail mise à jour avec succès.');
    }

    public function test(Request $request)
    {
        try {
            // Logique de test d'envoi d'email
            return response()->json(['message' => 'Test d\'envoi réussi']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function logs()
    {
        $logs = Log::channel('mail')->getLogger();
        return view('admin.mail.providers.phpmail.logs', compact('logs'));
    }

    public function clearLogs()
    {
        // Logique pour effacer les logs
        return redirect()->route('admin.mail.providers.phpmail.logs')
            ->with('success', 'Logs effacés avec succès.');
    }
}