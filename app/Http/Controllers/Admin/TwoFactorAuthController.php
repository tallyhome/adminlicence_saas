<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorAuthController extends Controller
{
    /**
     * Afficher la page de configuration de l'authentification à deux facteurs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $google2fa = new Google2FA();
        
        // Générer un nouveau secret si l'admin n'en a pas encore
        if (!$admin->two_factor_secret) {
            $admin->two_factor_secret = $google2fa->generateSecretKey();
            $admin->save();
        }
        
        // Générer l'URL otpauth
        $otpauthUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $admin->email,
            $admin->two_factor_secret
        );
        
        // Générer le QR code en SVG
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($otpauthUrl);
        
        // Convertir le SVG en URL data pour l'affichage dans une balise img
        $qrCodeUrl = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
        
        return view('admin.settings.two-factor', [
            'admin' => $admin,
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $admin->two_factor_secret
        ]);
    }
    
    /**
     * Activer l'authentification à deux facteurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $admin = Auth::guard('admin')->user();
        $google2fa = new Google2FA();
        
        // Vérifier le code fourni
        $valid = $google2fa->verifyKey($admin->two_factor_secret, $request->code);
        
        if (!$valid) {
            return back()->withErrors([
                'code' => 'Le code d\'authentification est invalide.'
            ]);
        }
        
        // Générer des codes de récupération
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::random(10);
        }
        
        $admin->two_factor_recovery_codes = json_encode($recoveryCodes);
        $admin->two_factor_enabled = true;
        $admin->save();
        
        return redirect()->route('admin.settings.two-factor')
            ->with('success', 'L\'authentification à deux facteurs a été activée avec succès.')
            ->with('recoveryCodes', $recoveryCodes);
    }
    
    /**
     * Désactiver l'authentification à deux facteurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $admin = Auth::guard('admin')->user();
        $google2fa = new Google2FA();
        
        // Vérifier le code fourni
        $valid = $google2fa->verifyKey($admin->two_factor_secret, $request->code);
        
        if (!$valid) {
            return back()->withErrors([
                'code' => 'Le code d\'authentification est invalide.'
            ]);
        }
        
        $admin->two_factor_enabled = false;
        $admin->two_factor_recovery_codes = null;
        $admin->save();
        
        return redirect()->route('admin.settings.two-factor')
            ->with('success', 'L\'authentification à deux facteurs a été désactivée avec succès.');
    }
    
    /**
     * Régénérer les codes de récupération.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerateRecoveryCodes()
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin->two_factor_enabled) {
            return redirect()->route('admin.settings.two-factor');
        }
        
        // Générer des codes de récupération
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::random(10);
        }
        
        $admin->two_factor_recovery_codes = json_encode($recoveryCodes);
        $admin->save();
        
        return redirect()->route('admin.settings.two-factor')
            ->with('success', 'Les codes de récupération ont été régénérés avec succès.')
            ->with('recoveryCodes', $recoveryCodes);
    }
    
    /**
     * Utiliser un code de récupération pour se connecter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function useRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);
        
        if (!session()->has('admin_2fa_id')) {
            return redirect()->route('admin.login');
        }
        
        $admin = \App\Models\Admin::find(session()->get('admin_2fa_id'));
        
        if (!$admin || !$admin->two_factor_enabled) {
            return redirect()->route('admin.login');
        }
        
        $recoveryCodes = json_decode($admin->two_factor_recovery_codes, true);
        
        // Vérifier si le code de récupération est valide
        $index = array_search($request->recovery_code, $recoveryCodes);
        
        if ($index === false) {
            return back()->withErrors([
                'recovery_code' => 'Le code de récupération est invalide.'
            ]);
        }
        
        // Supprimer le code utilisé
        unset($recoveryCodes[$index]);
        $admin->two_factor_recovery_codes = json_encode(array_values($recoveryCodes));
        $admin->save();
        
        // Authentifier l'administrateur
        Auth::guard('admin')->login($admin);
        
        // Supprimer l'ID de la session
        session()->forget('admin_2fa_id');
        
        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Vérifier le code d'authentification à deux facteurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $admin = Auth::guard('admin')->user();
        $google2fa = new Google2FA();

        // Vérifier le code fourni
        $valid = $google2fa->verifyKey($admin->two_factor_secret, $request->code);

        return response()->json([
            'valid' => $valid
        ]);
    }
}