<?php

namespace App\Services;

use App\Contracts\MailProviderInterface;
use App\Services\Mail\MailProviderFactory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class MailService
{
    /**
     * Instance du fournisseur d'email actuel
     *
     * @var MailProviderInterface|null
     */
    protected ?MailProviderInterface $provider = null;

    /**
     * Récupérer les paramètres de messagerie
     *
     * @return array
     */
    public function getSettings(): array
    {
        return [
            'provider' => config('mail.default', 'smtp'),
            'mail_driver' => config('mail.default', 'smtp'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_password' => config('mail.mailers.smtp.password'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mailgun' => [
                'api_key' => config('services.mailgun.key'),
                'domain' => config('services.mailgun.domain'),
            ],
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
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
        $provider = $settings['provider'] ?? 'smtp';
        $fromAddress = $settings['from_address'] ?? '';
        $fromName = $settings['from_name'] ?? '';

        // Mettre à jour la configuration du fournisseur
        switch ($provider) {
            case 'smtp':
                $this->updateSmtpConfig($settings['smtp'] ?? []);
                break;
            case 'mailgun':
                $this->updateMailgunConfig($settings['mailgun'] ?? []);
                break;
        }

        // Mettre à jour les informations d'expéditeur
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);
        Config::set('mail.default', $provider);

        // Mettre à jour le fichier .env
        $this->updateEnvFile(array_merge(
            ['MAIL_MAILER' => $provider],
            $this->getEnvUpdates($settings)
        ));
    }

    /**
     * Envoyer un email
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param array $options
     * @return bool
     */
    public function send(string $to, string $subject, string $content, array $options = []): bool
    {
        $provider = $this->getProvider();
        return $provider->send($to, $subject, $content, $options);
    }

    /**
     * Obtenir l'instance du fournisseur d'email actuel
     *
     * @return MailProviderInterface
     */
    protected function getProvider(): MailProviderInterface
    {
        if (!$this->provider) {
            $settings = $this->getSettings();
            $provider = $settings['provider'];
            $config = array_merge(
                $settings[$provider] ?? [],
                [
                    'from_address' => $settings['from_address'],
                    'from_name' => $settings['from_name'],
                ]
            );

            $this->provider = MailProviderFactory::create($provider, $config);
        }

        return $this->provider;
    }

    /**
     * Mettre à jour la configuration SMTP
     *
     * @param array $config
     * @return void
     */
    protected function updateSmtpConfig(array $config): void
    {
        Config::set('mail.mailers.smtp.host', $config['host'] ?? '');
        Config::set('mail.mailers.smtp.port', $config['port'] ?? '');
        Config::set('mail.mailers.smtp.username', $config['username'] ?? '');
        Config::set('mail.mailers.smtp.password', $config['password'] ?? '');
        Config::set('mail.mailers.smtp.encryption', $config['encryption'] ?? '');
    }

    /**
     * Mettre à jour la configuration Mailgun
     *
     * @param array $config
     * @return void
     */
    protected function updateMailgunConfig(array $config): void
    {
        Config::set('services.mailgun.key', $config['api_key'] ?? '');
        Config::set('services.mailgun.domain', $config['domain'] ?? '');
    }

    /**
     * Obtenir les mises à jour pour le fichier .env
     *
     * @param array $settings
     * @return array
     */
    protected function getEnvUpdates(array $settings): array
    {
        $updates = [
            'MAIL_FROM_ADDRESS' => $settings['from_address'] ?? '',
            'MAIL_FROM_NAME' => $settings['from_name'] ?? ''
        ];

        if (isset($settings['smtp'])) {
            $updates = array_merge($updates, [
                'MAIL_HOST' => $settings['smtp']['host'] ?? '',
                'MAIL_PORT' => $settings['smtp']['port'] ?? '',
                'MAIL_USERNAME' => $settings['smtp']['username'] ?? '',
                'MAIL_PASSWORD' => $settings['smtp']['password'] ?? '',
                'MAIL_ENCRYPTION' => $settings['smtp']['encryption'] ?? ''
            ]);
        }

        if (isset($settings['mailgun'])) {
            $updates = array_merge($updates, [
                'MAILGUN_DOMAIN' => $settings['mailgun']['domain'] ?? '',
                'MAILGUN_SECRET' => $settings['mailgun']['api_key'] ?? ''
            ]);
        }

        return $updates;
    }

    /**
     * Mettre à jour le fichier .env
     *
     * @param array $updates
     * @return void
     */
    protected function updateEnvFile(array $updates): void
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        foreach ($updates as $key => $value) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        }

        File::put($envPath, $envContent);
    }
}