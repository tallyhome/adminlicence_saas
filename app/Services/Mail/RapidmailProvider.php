<?php

namespace App\Services\Mail;

use App\Contracts\MailProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RapidmailProvider implements MailProviderInterface
{
    protected ?Client $client = null;
    protected array $config = [];
    protected string $apiUrl = 'https://apiv3.emailsys.net/v1';

    public function configure(array $config): void
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . ($config['api_key'] ?? ''),
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function send(string $to, string $subject, string $content, array $options = []): bool
    {
        try {
            if (!$this->client) {
                return false;
            }

            $payload = [
                'json' => [
                    'recipients' => [['email' => $to]],
                    'subject' => $subject,
                    'html_body' => $content,
                    'from' => [
                        'email' => $this->config['from_address'] ?? '',
                        'name' => $this->config['from_name'] ?? ''
                    ]
                ]
            ];

            // Gestion des pièces jointes
            if (isset($options['attachments'])) {
                $payload['json']['attachments'] = array_map(function($attachment) {
                    return [
                        'filename' => $attachment['name'] ?? basename($attachment['path']),
                        'content' => base64_encode(file_get_contents($attachment['path'])),
                        'type' => mime_content_type($attachment['path'])
                    ];
                }, $options['attachments']);
            }

            $response = $this->client->post('/email', $payload);
            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
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