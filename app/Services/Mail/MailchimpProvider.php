<?php

namespace App\Services\Mail;

use App\Contracts\MailProviderInterface;
use MailchimpTransactional\ApiClient;

class MailchimpProvider implements MailProviderInterface
{
    protected ?ApiClient $client = null;
    protected array $config = [];

    public function configure(array $config): void
    {
        $this->config = $config;
        $this->client = new ApiClient();
        $this->client->setApiKey($config['api_key'] ?? '');
    }

    public function send(string $to, string $subject, string $content, array $options = []): bool
    {
        try {
            if (!$this->client) {
                return false;
            }

            $message = [
                'from_email' => $this->config['from_address'] ?? '',
                'from_name' => $this->config['from_name'] ?? '',
                'subject' => $subject,
                'html' => $content,
                'to' => [
                    [
                        'email' => $to,
                        'type' => 'to'
                    ]
                ]
            ];

            // Gestion des pièces jointes
            if (isset($options['attachments'])) {
                $message['attachments'] = array_map(function($attachment) {
                    return [
                        'type' => mime_content_type($attachment['path']),
                        'name' => $attachment['name'] ?? basename($attachment['path']),
                        'content' => base64_encode(file_get_contents($attachment['path']))
                    ];
                }, $options['attachments']);
            }

            $response = $this->client->messages->send(['message' => $message]);
            return isset($response['_id']);
        } catch (\Exception $e) {
            // Log l'erreur ou la gérer selon les besoins
            return false;
        }
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function validateConfig(): bool
    {
        return !empty($this->config['api_key']) && 
               !empty($this->config['from_address']) && 
               !empty($this->config['from_name']);
    }
}