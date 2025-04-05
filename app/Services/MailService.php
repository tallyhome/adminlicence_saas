<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class MailService
{
    /**
     * Récupérer les paramètres de messagerie
     *
     * @return array
     */
    public function getSettings(): array
    {
        return [
            'mail_driver' => config('mail.mailers.smtp.driver'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_password' => config('mail.mailers.smtp.password'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];
    }

    /**
     * Mettre à jour les paramètres de messagerie
     *
     * @param array $settings
     * @return void
     */
    public function updateSettings(array $settings): void
    {
        // Mettre à jour la configuration
        Config::set('mail.mailers.smtp.driver', $settings['mail_driver']);
        Config::set('mail.mailers.smtp.host', $settings['mail_host']);
        Config::set('mail.mailers.smtp.port', $settings['mail_port']);
        Config::set('mail.mailers.smtp.username', $settings['mail_username']);
        Config::set('mail.mailers.smtp.password', $settings['mail_password']);
        Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption']);
        Config::set('mail.from.address', $settings['mail_from_address']);
        Config::set('mail.from.name', $settings['mail_from_name']);

        // Mettre à jour le fichier .env
        $this->updateEnvFile($settings);
    }

    /**
     * Mettre à jour le fichier .env avec les nouveaux paramètres
     *
     * @param array $settings
     * @return void
     */
    protected function updateEnvFile(array $settings): void
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        $envContent = preg_replace(
            [
                '/MAIL_MAILER=.*/',
                '/MAIL_HOST=.*/',
                '/MAIL_PORT=.*/',
                '/MAIL_USERNAME=.*/',
                '/MAIL_PASSWORD=.*/',
                '/MAIL_ENCRYPTION=.*/',
                '/MAIL_FROM_ADDRESS=.*/',
                '/MAIL_FROM_NAME=.*/',
            ],
            [
                'MAIL_MAILER=' . $settings['mail_driver'],
                'MAIL_HOST=' . $settings['mail_host'],
                'MAIL_PORT=' . $settings['mail_port'],
                'MAIL_USERNAME=' . $settings['mail_username'],
                'MAIL_PASSWORD=' . $settings['mail_password'],
                'MAIL_ENCRYPTION=' . $settings['mail_encryption'],
                'MAIL_FROM_ADDRESS=' . $settings['mail_from_address'],
                'MAIL_FROM_NAME=' . $settings['mail_from_name'],
            ],
            $envContent
        );

        File::put($envPath, $envContent);
    }
} 