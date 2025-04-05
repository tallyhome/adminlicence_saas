<?php

namespace App\Services;

use App\Models\SerialKey;
use App\Notifications\LicenceStatusChanged;
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
            $key->save();
        }

        if (!$key->ip_address && $ipAddress) {
            $key->ip_address = $ipAddress;
            $key->save();
        }

        return [
            'valid' => true,
            'message' => 'Clé de série valide',
            'status_code' => 200,
            'data' => [
                'project_id' => $key->project_id,
                'expires_at' => $key->expires_at,
                'domain' => $key->domain,
                'ip_address' => $key->ip_address
            ]
        ];
    }

    /**
     * Suspendre une clé de série.
     *
     * @param SerialKey $serialKey
     * @return void
     */
    public function suspendKey(SerialKey $serialKey): void
    {
        $serialKey->update([
            'status' => 'suspended'
        ]);

        // Notifier le propriétaire du projet
        if ($serialKey->project->user) {
            $serialKey->project->user->notify(new LicenceStatusChanged($serialKey, 'suspended'));
        }
    }

    /**
     * Révoquer une clé de série.
     *
     * @param SerialKey $serialKey
     * @return void
     */
    public function revokeKey(SerialKey $serialKey): void
    {
        $serialKey->update([
            'status' => 'revoked'
        ]);

        // Notifier le propriétaire du projet
        if ($serialKey->project->user) {
            $serialKey->project->user->notify(new LicenceStatusChanged($serialKey, 'revoked'));
        }
    }

    /**
     * Activer une clé de série.
     *
     * @param SerialKey $serialKey
     * @return bool
     */
    public function activateKey(SerialKey $serialKey): bool
    {
        if ($serialKey->status === 'revoked') {
            return false;
        }

        $serialKey->update([
            'status' => 'active'
        ]);

        // Notifier le propriétaire du projet
        if ($serialKey->project->user) {
            $serialKey->project->user->notify(new LicenceStatusChanged($serialKey, 'active'));
        }

        return true;
    }

    /**
     * Générer un code sécurisé pour une clé de série.
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
                'success' => false,
                'message' => 'Clé de série invalide'
            ];
        }

        $secureCode = $this->createSecureCode($key->id);

        // Stocker le code dans le cache pendant 5 minutes
        Cache::put("secure_code_{$key->id}", $secureCode, 300);

        return [
            'success' => true,
            'secure_code' => $secureCode
        ];
    }

    /**
     * Créer un code sécurisé pour une clé de série.
     *
     * @param int $keyId
     * @return string
     */
    private function createSecureCode(int $keyId): string
    {
        return hash('sha256', $keyId . time() . Str::random(32));
    }

    /**
     * Génère une nouvelle clé de licence unique
     *
     * @return string
     */
    public function generateKey(): string
    {
        do {
            $key = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (SerialKey::where('serial_key', $key)->exists());

        return $key;
    }
}