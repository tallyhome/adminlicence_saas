<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Http;

class MailgunProvider extends AbstractMailProvider
{
    /**
     * Champs requis pour la configuration Mailgun
     *
     * @var array
     */
    protected array $requiredFields = [
        'api_key',
        'domain',
        'from_address',
        'from_name'
    ];

    /**
     * URL de base de l'API Mailgun
     *
     * @var string
     */
    protected string $apiUrl = 'https://api.mailgun.net/v3';

    /**
     * Envoyer un email via Mailgun
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param array $options
     * @return bool
     */
    public function send(string $to, string $subject, string $content, array $options = []): bool
    {
        if (!$this->validateConfig()) {
            return false;
        }

        try {
            $options = $this->prepareMailOptions($options);
            $response = Http::withBasicAuth('api', $this->config['api_key'])
                ->asForm()
                ->post("{$this->apiUrl}/{$this->config['domain']}/messages", [
                    'from' => "{$options['from']['name']} <{$options['from']['email']}>",
                    'to' => $to,
                    'subject' => $subject,
                    'text' => strip_tags($content),
                    'html' => $content,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            // Log l'erreur ou la gérer selon les besoins
            return false;
        }
    }

    /**
     * Vérifier si la configuration est valide
     *
     * @return bool
     */
    public function validateConfig(): bool
    {
        return $this->validateRequiredFields($this->requiredFields);
    }
}