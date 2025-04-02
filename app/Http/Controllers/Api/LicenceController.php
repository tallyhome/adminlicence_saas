<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SerialKey;
use App\Services\LicenceService;
use Illuminate\Http\Request;

class LicenceController extends Controller
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
     * Récupérer le code dynamique sécurisé pour l'application cliente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\LicenceService  $licenceService
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSecureCode(Request $request, LicenceService $licenceService)
    {
        $request->validate([
            'token' => 'required|string',
            'serial_key' => 'required|string',
        ]);

        $result = $licenceService->generateSecureCode(
            $request->serial_key,
            $request->token
        );

        if (!$result['valid']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], $result['status_code']);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'secure_code' => $result['secure_code'],
                'valid_until' => $result['valid_until'],
            ],
        ]);
    }


}