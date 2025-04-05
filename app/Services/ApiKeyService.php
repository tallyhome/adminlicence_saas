<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ApiKeyService
{
    /**
     * Générer une nouvelle clé API.
     *
     * @param Project $project
     * @param string $name
     * @param array $permissions
     * @return ApiKey
     */
    public function generateKey(Project $project, string $name, array $permissions = []): ApiKey
    {
        $key = new ApiKey([
            'project_id' => $project->id,
            'name' => $name,
            'key' => 'sk_' . Str::random(32),
            'secret' => 'sk_' . Str::random(32),
            'permissions' => $permissions,
            'last_used_at' => null,
            'expires_at' => null,
        ]);

        $key->save();

        return $key;
    }

    /**
     * Valider une clé API.
     *
     * @param string $key
     * @param string $secret
     * @return bool
     */
    public function validateKey(string $key, string $secret): bool
    {
        $apiKey = ApiKey::where('key', $key)->first();

        if (!$apiKey) {
            return false;
        }

        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return false;
        }

        if ($apiKey->revoked_at) {
            return false;
        }

        return Hash::check($secret, $apiKey->secret);
    }

    /**
     * Révocation d'une clé API.
     *
     * @param ApiKey $apiKey
     * @return bool
     */
    public function revokeKey(ApiKey $apiKey): bool
    {
        $apiKey->revoked_at = now();
        return $apiKey->save();
    }

    /**
     * Réactivation d'une clé API.
     *
     * @param ApiKey $apiKey
     * @return bool
     */
    public function reactivateKey(ApiKey $apiKey): bool
    {
        $apiKey->revoked_at = null;
        return $apiKey->save();
    }

    /**
     * Mise à jour des permissions d'une clé API.
     *
     * @param ApiKey $apiKey
     * @param array $permissions
     * @return bool
     */
    public function updatePermissions(ApiKey $apiKey, array $permissions): bool
    {
        $apiKey->permissions = $permissions;
        return $apiKey->save();
    }

    /**
     * Enregistrer l'utilisation d'une clé API.
     *
     * @param ApiKey $apiKey
     * @return bool
     */
    public function logUsage(ApiKey $apiKey): bool
    {
        $apiKey->last_used_at = now();
        $apiKey->increment('usage_count');
        return $apiKey->save();
    }

    /**
     * Obtenir les statistiques d'utilisation d'une clé API.
     *
     * @param ApiKey $apiKey
     * @return array
     */
    public function getUsageStats(ApiKey $apiKey): array
    {
        return [
            'total_usage' => $apiKey->usage_count,
            'last_used' => $apiKey->last_used_at,
            'created_at' => $apiKey->created_at,
            'expires_at' => $apiKey->expires_at,
            'status' => $this->getKeyStatus($apiKey),
        ];
    }

    /**
     * Obtenir le statut d'une clé API.
     *
     * @param ApiKey $apiKey
     * @return string
     */
    private function getKeyStatus(ApiKey $apiKey): string
    {
        if ($apiKey->revoked_at) {
            return 'revoked';
        }

        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return 'expired';
        }

        return 'active';
    }
} 