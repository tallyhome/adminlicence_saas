<?php

namespace App\Contracts;

interface MailProviderInterface
{
    /**
     * Configurer le fournisseur d'email avec les paramètres donnés
     *
     * @param array $config
     * @return void
     */
    public function configure(array $config): void;

    /**
     * Envoyer un email
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param array $options
     * @return bool
     */
    public function send(string $to, string $subject, string $content, array $options = []): bool;

    /**
     * Obtenir la configuration actuelle du fournisseur
     *
     * @return array
     */
    public function getConfig(): array;

    /**
     * Vérifier si la configuration est valide
     *
     * @return bool
     */
    public function validateConfig(): bool;
}