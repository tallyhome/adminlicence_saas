<?php

namespace App\Services;

use App\Models\SerialKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LicenceService
{
    /**
     * Vérifie si une clé de série est valide.
     *
     * @param string $serialKey
     * @param string|null $domain
     * @param string|null $ipAddress
     * @return array
     */
    public function validateSerialKey(string $serialKey, ?string $domain = null, ?string $ipAddress = null): array
    {
        $key = SerialKey::where('serial_key', $serialKey)->first();

        if (!$key) {
            return [
                'valid' => false,
                'message' => 'Clé de série invalide',
                'status_code' => 404
            ];
        }

        if (!$key->isValid()) {
            return [
                'valid' => false,
                'message' => 'Clé de série expirée ou révoquée',
                'status_code' => 403
            ];
        }

        // Vérification du domaine si fourni
        if ($domain && !$key->isDomainAuthorized($domain)) {
            return [
                'valid' => false,
                'message' => 'Domaine non autorisé pour cette clé',
                'status_code' => 403
            ];
        }

        // Vérification de l'adresse IP si fournie
        if ($ipAddress && !$key->isIpAuthorized($ipAddress)) {
            return [
                'valid' => false,
                'message' => 'Adresse IP non autorisée pour cette clé',
                'status_code' => 403
            ];
        }

        // Mise à jour du domaine et de l'IP si la clé n'est pas encore liée
        if (!$key->domain && $domain) {
            $key->domain = $domain;
        }

        if (!$key->ip_address && $ipAddress) {
            $key->ip_address = $ipAddress;
        }

        $key->save();

        // Générer un token temporaire pour cette clé
        $token = Str::random(64);
        Cache::put('licence_token_' . $key->id, $token, now()->addHours(24));

        return [
            'valid' => true,
            'token' => $token,
            'project' => $key->project->name,
            'expires_at' => $key->expires_at,
            'status_code' => 200
        ];
    }

    /**
     * Génère un code sécurisé dynamique pour une clé de série.
     *
     * @param string $serialKey
     * @param string $token
     * @return array
     */
    public function generateSecureCode(string $serialKey, string $token): array
    {
        $key = SerialKey::where('serial_key', $serialKey)->first();

        if (!$key) {
            return [
                'valid' => false,
                'message' => 'Clé de série invalide',
                'status_code' => 404
            ];
        }

        // Vérifier le token en cache
        $cachedToken = Cache::get('licence_token_' . $key->id);
        if (!$cachedToken || $cachedToken !== $token) {
            return [
                'valid' => false,
                'message' => 'Token invalide ou expiré',
                'status_code' => 403
            ];
        }

        // Générer un code dynamique valide pour 1 heure
        $secureCode = $this->createSecureCode($key->id);
        
        return [
            'valid' => true,
            'secure_code' => $secureCode,
            'valid_until' => now()->addHour()->toIso8601String(),
            'status_code' => 200
        ];
    }

    /**
     * Crée un code sécurisé dynamique basé sur l'ID de la clé et l'heure actuelle.
     *
     * @param int $keyId
     * @return string
     */
    private function createSecureCode(int $keyId): string
    {
        // Utiliser l'heure actuelle arrondie à l'heure pour que le code change chaque heure
        $hourTimestamp = now()->startOfHour()->timestamp;
        
        // Créer une chaîne unique basée sur l'ID de la clé et l'heure
        $baseString = $keyId . '_' . $hourTimestamp . '_' . config('app.key');
        
        // Générer un hash et prendre les 16 premiers caractères
        return substr(hash('sha256', $baseString), 0, 16);
    }

    /**
     * Révoque une clé de série.
     *
     * @param SerialKey $serialKey
     * @return bool
     */
    public function revokeKey(SerialKey $serialKey): bool
    {
        $serialKey->status = 'revoked';
        return $serialKey->save();
    }

    /**
     * Suspend une clé de série.
     *
     * @param SerialKey $serialKey
     * @return bool
     */
    public function suspendKey(SerialKey $serialKey): bool
    {
        $serialKey->status = 'suspended';
        return $serialKey->save();
    }

    /**
     * Réactive une clé de série.
     *
     * @param SerialKey $serialKey
     * @return bool
     */
    public function activateKey(SerialKey $serialKey): bool
    {
        $serialKey->status = 'active';
        return $serialKey->save();
    }
}