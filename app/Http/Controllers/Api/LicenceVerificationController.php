<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Licence;
use App\Models\LicenceActivation;
use App\Services\LicenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class LicenceVerificationController extends Controller
{
    protected $licenceService;

    /**
     * Constructeur du contrôleur de vérification de licence
     */
    public function __construct(LicenceService $licenceService)
    {
        $this->licenceService = $licenceService;
    }

    /**
     * Vérifie si une licence est valide
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'licence_key' => 'required|string',
            'product_id' => 'required|integer',
            'device_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données de validation invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $licence = Licence::where('licence_key', $request->licence_key)
                         ->where('product_id', $request->product_id)
                         ->first();

        if (!$licence) {
            return response()->json([
                'success' => false,
                'message' => 'Licence invalide ou inexistante'
            ], 404);
        }

        // Vérifier si la licence est active
        if (!$licence->isActive()) {
            return response()->json([
                'success' => false,
                'message' => $licence->isExpired() ? 'Licence expirée' : 'Licence inactive ou révoquée',
                'status' => $licence->status
            ], 403);
        }

        // Mettre à jour la date de dernière vérification
        $licence->updateLastCheck();

        return response()->json([
            'success' => true,
            'message' => 'Licence valide',
            'data' => [
                'licence_key' => $licence->licence_key,
                'status' => $licence->status,
                'expires_at' => $licence->expires_at,
                'max_activations' => $licence->max_activations,
                'current_activations' => $licence->current_activations,
            ]
        ]);
    }

    /**
     * Active une licence sur un appareil
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function activate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'licence_key' => 'required|string',
            'product_id' => 'required|integer',
            'device_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données de validation invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $licence = Licence::where('licence_key', $request->licence_key)
                         ->where('product_id', $request->product_id)
                         ->first();

        if (!$licence) {
            return response()->json([
                'success' => false,
                'message' => 'Licence invalide ou inexistante'
            ], 404);
        }

        // Vérifier si la licence est active
        if (!$licence->isActive()) {
            return response()->json([
                'success' => false,
                'message' => $licence->isExpired() ? 'Licence expirée' : 'Licence inactive ou révoquée',
                'status' => $licence->status
            ], 403);
        }

        // Vérifier si la licence peut être activée sur un nouvel appareil
        if (!$licence->canActivate()) {
            return response()->json([
                'success' => false,
                'message' => 'Nombre maximum d\'activations atteint pour cette licence'
            ], 403);
        }

        // Générer un identifiant unique pour l'appareil
        $deviceId = LicenceActivation::generateDeviceId(
            $request->header('User-Agent', 'Unknown'),
            $request->ip()
        );

        // Vérifier si l'appareil est déjà activé
        $existingActivation = LicenceActivation::where('licence_id', $licence->id)
                                              ->where('device_id', $deviceId)
                                              ->where('is_active', true)
                                              ->first();

        if ($existingActivation) {
            return response()->json([
                'success' => true,
                'message' => 'Licence déjà activée sur cet appareil',
                'data' => [
                    'activation_id' => $existingActivation->id,
                    'device_name' => $existingActivation->device_name,
                    'activated_at' => $existingActivation->activated_at
                ]
            ]);
        }

        // Créer une nouvelle activation
        $activation = LicenceActivation::create([
            'licence_id' => $licence->id,
            'device_id' => $deviceId,
            'device_name' => $request->device_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent', 'Unknown'),
            'is_active' => true,
            'activated_at' => now(),
            'metadata' => [
                'os' => $request->input('os', 'Unknown'),
                'app_version' => $request->input('app_version', 'Unknown')
            ]
        ]);

        // Incrémenter le compteur d'activations
        $licence->incrementActivations();

        return response()->json([
            'success' => true,
            'message' => 'Licence activée avec succès',
            'data' => [
                'activation_id' => $activation->id,
                'device_name' => $activation->device_name,
                'activated_at' => $activation->activated_at
            ]
        ]);
    }

    /**
     * Désactive une licence sur un appareil
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deactivate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'licence_key' => 'required|string',
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données de validation invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $licence = Licence::where('licence_key', $request->licence_key)
                         ->where('product_id', $request->product_id)
                         ->first();

        if (!$licence) {
            return response()->json([
                'success' => false,
                'message' => 'Licence invalide ou inexistante'
            ], 404);
        }

        // Générer un identifiant unique pour l'appareil
        $deviceId = LicenceActivation::generateDeviceId(
            $request->header('User-Agent', 'Unknown'),
            $request->ip()
        );

        // Rechercher l'activation pour cet appareil
        $activation = LicenceActivation::where('licence_id', $licence->id)
                                      ->where('device_id', $deviceId)
                                      ->where('is_active', true)
                                      ->first();

        if (!$activation) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune activation trouvée pour cet appareil'
            ], 404);
        }

        // Désactiver l'activation
        $activation->deactivate();

        return response()->json([
            'success' => true,
            'message' => 'Licence désactivée avec succès',
            'data' => [
                'deactivated_at' => $activation->deactivated_at
            ]
        ]);
    }
}
