<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SmtpMailProvider extends AbstractMailProvider
{
    /**
     * Champs requis pour la configuration SMTP
     *
     * @var array
     */
    protected array $requiredFields = [
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name'
    ];

    /**
     * Envoyer un email via SMTP
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param array $options
     * @return bool
     */
    public function send(string $to, string $subject, string $content, array $options = []): bool
    {
        try {
            $this->applyConfig();
            
            Mail::raw($content, function($message) use ($to, $subject, $options) {
                $message->to($to)
                        ->subject($subject);
                
                $options = $this->prepareMailOptions($options);
                
                if (!empty($options['from']['email'])) {
                    $message->from($options['from']['email'], $options['from']['name'] ?? null);
                }

                if (!empty($options['cc'])) {
                    $message->cc($options['cc']);
                }

                if (!empty($options['bcc'])) {
                    $message->bcc($options['bcc']);
                }

                if (!empty($options['attachments'])) {
                    foreach ($options['attachments'] as $attachment) {
                        $message->attach($attachment);
                    }
                }
            });

            return true;
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

    /**
     * Appliquer la configuration au mailer Laravel
     *
     * @return void
     */
    protected function applyConfig(): void
    {
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $this->config['host']);
        Config::set('mail.mailers.smtp.port', $this->config['port']);
        Config::set('mail.mailers.smtp.username', $this->config['username']);
        Config::set('mail.mailers.smtp.password', $this->config['password']);
        Config::set('mail.mailers.smtp.encryption', $this->config['encryption']);
        Config::set('mail.from.address', $this->config['from_address']);
        Config::set('mail.from.name', $this->config['from_name']);
    }
}