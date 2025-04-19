<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Licence;
use App\Models\LicenceActivation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LicenceVerificationController extends Controller
{
    /**
     * Vérifier si une licence est valide
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'licence_key' => 'required|string',
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données de validation incorrectes',
                'errors' => $validator->errors(),
            ], 422);
        }

        $licenceKey = $request->input('licence_key');
        $productId = $request->input('product_id');

        // Recherche de la licence
        $licence = Licence::where('licence_key', $licenceKey)
            ->where('product_id', $productId)
            ->first();

        if (!$licence) {
            return response()->json([
                'success' => false,
                'message' => 'Licence invalide ou introuvable',
            ], 404);
        }

        // Vérification du statut de la licence
        if ($licence->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Licence ' . $this->getStatusMessage($licence->status),
                'status' => $licence->status,
            ], 403);
        }

        // Vérification de l'expiration
        if ($licence->expires_at && $licence->expires_at->isPast()) {
            // Mise à jour du statut de la licence
            $licence->status = 'expired';
            $licence->save();

            return response()->json([
                'success' => false,
                'message' => 'Licence expirée',
                'status' => 'expired',
                'expires_at' => $licence->expires_at->format('Y-m-d'),
            ], 403);
        }

        // Mise à jour de la date de dernière vérification
        $licence->last_check_at = now();
        $licence->save();

        return response()->json([
            'success' => true,
            'message' => 'Licence valide',
            'licence' => [
                'status' => $licence->status,
                'expires_at' => $licence->expires_at ? $licence->expires_at->format('Y-m-d') : null,
                'max_activations' => $licence->max_activations,
                'current_activations' => $licence->current_activations,
            ],
        ]);
    }

    /**
     * Activer une licence sur un appareil
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'licence_key' => 'required|string',
            'product_id' => 'required|integer',
            'device_id' => 'required|string',
            'device_name' => 'required|string',
            'ip_address' => 'nullable|ip',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données de validation incorrectes',
                'errors' => $validator->errors(),
            ], 422);
        }

        $licenceKey = $request->input('licence_key');
        $productId = $request->input('product_id');
        $deviceId = $request->input('device_id');
        $deviceName = $request->input('device_name');
        $ipAddress = $request->input('ip_address', $request->ip());

        // Recherche de la licence
        $licence = Licence::where('licence_key', $licenceKey)
            ->where('product_id', $productId)
            ->first();

        if (!$licence) {
            return response()->json([
                'success' => false,
                'message' => 'Licence invalide ou introuvable',
            ], 404);
        }

        // Vérification du statut de la licence
        if ($licence->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Licence ' . $this->getStatusMessage($licence->status),
                'status' => $licence->status,
            ], 403);
        }

        // Vérification de l'expiration
        if ($licence->expires_at && $licence->expires_at->isPast()) {
            // Mise à jour du statut de la licence
            $licence->status = 'expired';
            $licence->save();

            return response()->json([
                'success' => false,
                'message' => 'Licence expirée',
                'status' => 'expired',
            ], 403);
        }

        // Vérification si l'appareil est déjà activé
        $existingActivation = LicenceActivation::where('licence_id', $licence->id)
            ->where('device_id', $deviceId)
            ->first();

        if ($existingActivation) {
            // Si l'activation existe mais est inactive, on la réactive
            if (!$existingActivation->is_active) {
                $existingActivation->is_active = true;
                $existingActivation->activated_at = now();
                $existingActivation->deactivated_at = null;
                $existingActivation->save();

                // Mise à jour du compteur d'activations
                $licence->current_activations = $licence->activations()->where('is_active', true)->count();
                $licence->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Licence réactivée avec succès sur cet appareil',
                    'activation_id' => $existingActivation->id,
                ]);
            }

            // Si l'activation est déjà active, on renvoie simplement un succès
            return response()->json([
                'success' => true,
                'message' => 'Licence déjà activée sur cet appareil',
                'activation_id' => $existingActivation->id,
            ]);
        }

        // Vérification du nombre maximum d'activations
        if ($licence->max_activations && $licence->current_activations >= $licence->max_activations) {
            return response()->json([
                'success' => false,
                'message' => 'Nombre maximum d\'activations atteint',
                'current_activations' => $licence->current_activations,
                'max_activations' => $licence->max_activations,
            ], 403);
        }

        // Création d'une nouvelle activation
        $activation = new LicenceActivation([
            'licence_id' => $licence->id,
            'device_id' => $deviceId,
            'device_name' => $deviceName,
            'ip_address' => $ipAddress,
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $activation->save();

        // Mise à jour du compteur d'activations
        $licence->current_activations = $licence->activations()->where('is_active', true)->count();
        $licence->last_check_at = now();
        $licence->save();

        return response()->json([
            'success' => true,
            'message' => 'Licence activée avec succès',
            'activation_id' => $activation->id,
        ]);
    }

    /**
     * Désactiver une licence sur un appareil
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'licence_key' => 'required|string',
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données de validation incorrectes',
                'errors' => $validator->errors(),
            ], 422);
        }

        $licenceKey = $request->input('licence_key');
        $deviceId = $request->input('device_id');

        // Recherche de la licence
        $licence = Licence::where('licence_key', $licenceKey)->first();

        if (!$licence) {
            return response()->json([
                'success' => false,
                'message' => 'Licence invalide ou introuvable',
            ], 404);
        }

        // Recherche de l'activation
        $activation = LicenceActivation::where('licence_id', $licence->id)
            ->where('device_id', $deviceId)
            ->where('is_active', true)
            ->first();

        if (!$activation) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune activation active trouvée pour cet appareil',
            ], 404);
        }

        // Désactivation
        $activation->is_active = false;
        $activation->deactivated_at = now();
        $activation->save();

        // Mise à jour du compteur d'activations
        $licence->current_activations = $licence->activations()->where('is_active', true)->count();
        $licence->save();

        return response()->json([
            'success' => true,
            'message' => 'Licence désactivée avec succès',
        ]);
    }

    /**
     * Obtenir un message d'erreur en fonction du statut de la licence
     *
     * @param  string  $status
     * @return string
     */
    private function getStatusMessage($status)
    {
        switch ($status) {
            case 'expired':
                return 'expirée';
            case 'suspended':
                return 'suspendue';
            case 'revoked':
                return 'révoquée';
            default:
                return 'invalide';
        }
    }
}
