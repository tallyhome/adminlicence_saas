<?php

use App\Services\TranslationService;

if (!function_exists('t')) {
    /**
     * Traduire une clé
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function t(string $key, array $replace = [], ?string $locale = null): string
    {
        return app(TranslationService::class)->translate($key, $replace, $locale);
    }
}

if (!function_exists('get_translations')) {
    /**
     * Obtenir toutes les traductions pour la langue active
     *
     * @param string|null $locale
     * @return array
     */
    function get_translations(?string $locale = null): array
    {
        return app(TranslationService::class)->getTranslations($locale);
    }
}

if (!function_exists('get_available_locales')) {
    /**
     * Obtenir la liste des langues disponibles
     *
     * @return array
     */
    function get_available_locales(): array
    {
        return app(TranslationService::class)->getAvailableLocales();
    }
}

if (!function_exists('get_locale_name')) {
    /**
     * Obtenir le nom d'une langue dans sa langue native
     *
     * @param string $locale
     * @return string
     */
    function get_locale_name(string $locale): string
    {
        return app(TranslationService::class)->getLocaleName($locale);
    }
}