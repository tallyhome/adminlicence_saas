<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SerialKey;
use App\Services\LicenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LicenceApiController extends Controller
{
    /**
     * Vérifier la validité d'une clé de série.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\LicenceService  $licenceService
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSerial(Request $request, LicenceService $licenceService)
    {
        // Log de la requête pour le débogage
        Log::info('API check-serial appelée', [
            'ip' => $request->ip(),
            'data' => $request->all()
        ]);

        $request->validate([
            'serial_key' => 'required|string',
            'domain' => 'nullable|string',
            'ip_address' => 'nullable|string|ip',
        ]);

        $result = $licenceService->validateSerialKey(
            $request->serial_key,
            $request->domain,
            $request->ip_address
        );

        if (!$result['valid']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], $result['status_code']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Clé de série valide',
            'data' => [
                'token' => $result['token'],
                'project' => $result['project'],
                'expires_at' => $result['expires_at'],
            ],
        ]);
    }

    /**
     * Route de test simple pour vérifier que l'API fonctionne.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'API AdminLicence fonctionne correctement',
            'version' => config('version.full'),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }
}
