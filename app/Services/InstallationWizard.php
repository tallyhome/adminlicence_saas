<?php

namespace App\Services;

use App\Models\User;
use App\Models\MailConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class InstallationWizard
{
    /**
     * Vérifier si l'application est déjà installée
     *
     * @return bool
     */
    public function isInstalled(): bool
    {
        try {
            return DB::table('users')->exists() && 
                   DB::table('mail_configs')->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Exécuter les migrations de base de données
     *
     * @return bool
     */
    public function runMigrations(): bool
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Créer le compte administrateur initial
     *
     * @param array $adminData
     * @return bool
     */
    public function createAdminAccount(array $adminData): bool
    {
        try {
            User::create([
                'name' => $adminData['name'],
                'email' => $adminData['email'],
                'password' => Hash::make($adminData['password']),
                'role' => 'admin'
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Configurer les paramètres de messagerie initiaux
     *
     * @param array $mailConfig
     * @return bool
     */
    public function setupMailConfiguration(array $mailConfig): bool
    {
        try {
            $mailService = new MailService();
            $mailService->updateSettings($mailConfig);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Configurer les paramètres de langue
     *
     * @param string $defaultLocale
     * @return bool
     */
    public function setupLocalization(string $defaultLocale): bool
    {
        try {
            $translationManager = new TranslationManager();
            $translationManager->setDefaultLocale($defaultLocale);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Exécuter l'installation complète
     *
     * @param array $config Configuration complète pour l'installation
     * @return array [success => bool, message => string]
     */
    public function install(array $config): array
    {
        if ($this->isInstalled()) {
            return [
                'success' => false,
                'message' => 'L\'application est déjà installée.'
            ];
        }

        DB::beginTransaction();

        try {
            // Exécuter les migrations
            if (!$this->runMigrations()) {
                throw new \Exception('Échec des migrations de la base de données.');
            }

            // Créer le compte administrateur
            if (!$this->createAdminAccount($config['admin'])) {
                throw new \Exception('Échec de la création du compte administrateur.');
            }

            // Configurer la messagerie
            if (!$this->setupMailConfiguration($config['mail'])) {
                throw new \Exception('Échec de la configuration de la messagerie.');
            }

            // Configurer la localisation
            if (!$this->setupLocalization($config['locale'])) {
                throw new \Exception('Échec de la configuration de la localisation.');
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Installation terminée avec succès.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Erreur lors de l\'installation : ' . $e->getMessage()
            ];
        }
    }
}