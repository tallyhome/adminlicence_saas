<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    /**
     * Affiche la page de test pour Google2FA
     *
     * @return \Illuminate\View\View
     */
    public function testGoogle2FA()
    {
        $google2fa = new Google2FA();
        
        // Générer une clé secrète
        $secret = $google2fa->generateSecretKey();
        
        // Générer l'URL du QR code
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            'test@example.com',
            $secret
        );
        
        // URL pour l'API Google Chart
        $googleChartUrl = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($qrCodeUrl);
        
        return view('admin.settings.test-google2fa', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
            'googleChartUrl' => $googleChartUrl
        ]);
    }
    
    /**
     * Vérifie si un code 2FA est valide
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
        ]);
        
        $google2fa = new Google2FA();
        
        $valid = $google2fa->verifyKey($request->secret, $request->code);
        
        return response()->json([
            'valid' => $valid
        ]);
    }
}