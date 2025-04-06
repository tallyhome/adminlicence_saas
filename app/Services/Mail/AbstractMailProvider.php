<?php

namespace App\Services\Mail;

use App\Contracts\MailProviderInterface;

abstract class AbstractMailProvider implements MailProviderInterface
{
    /**
     * Configuration du fournisseur
     *
     * @var array
     */
    protected array $config = [];

    /**
     * Configurer le fournisseur d'email
     *
     * @param array $config
     * @return void
     */
    public function configure(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Obtenir la configuration actuelle
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Valider les champs requis dans la configuration
     *
     * @param array $required
     * @return bool
     */
    protected function validateRequiredFields(array $required): bool
    {
        foreach ($required as $field) {
            if (!isset($this->config[$field]) || empty($this->config[$field])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Préparer les options d'envoi d'email
     *
     * @param array $options
     * @return array
     */
    protected function prepareMailOptions(array $options): array
    {
        return array_merge([
            'from' => [
                'email' => $this->config['from_address'] ?? null,
                'name' => $this->config['from_name'] ?? null,
            ],
        ], $options);
    }
}