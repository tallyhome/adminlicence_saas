<?php

namespace App\Services\Mail;

use App\Contracts\MailProviderInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PhpMailProvider implements MailProviderInterface
{
    protected PHPMailer $mailer;
    protected array $config;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
    }

    public function configure(array $config): void
    {
        $this->config = $config;

        $this->mailer->isSMTP();
        $this->mailer->Host = $config['host'] ?? '';
        $this->mailer->Port = $config['port'] ?? 587;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $config['username'] ?? '';
        $this->mailer->Password = $config['password'] ?? '';
        $this->mailer->SMTPSecure = $config['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;

        $this->mailer->setFrom(
            $config['from_address'] ?? '',
            $config['from_name'] ?? ''
        );
    }

    public function send(string $to, string $subject, string $content, array $options = []): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $content;
            $this->mailer->isHTML(true);

            // Gestion des pièces jointes
            if (isset($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    if (isset($attachment['path'])) {
                        $this->mailer->addAttachment(
                            $attachment['path'],
                            $attachment['name'] ?? ''
                        );
                    }
                }
            }

            return $this->mailer->send();
        } catch (Exception $e) {
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
        $required = ['host', 'port', 'username', 'password', 'from_address'];
        
        foreach ($required as $field) {
            if (empty($this->config[$field])) {
                return false;
            }
        }

        return true;
    }
}