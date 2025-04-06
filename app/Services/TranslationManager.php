<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class TranslationManager
{
    protected array $availableLocales = [
        'fr' => 'Français',
        'en' => 'English',
        'de' => 'Deutsch',
        'es' => 'Español',
        'it' => 'Italiano',
        'nl' => 'Nederlands',
        'pt' => 'Português',
        'ru' => 'Русский',
        'zh' => '中文',
        'ja' => '日本語'
    ];

    protected string $translationsPath;
    protected string $defaultLocale = 'fr';

    public function __construct()
    {
        $this->translationsPath = resource_path('locales');
    }

    /**
     * Obtenir toutes les langues disponibles
     *
     * @return array
     */
    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    /**
     * Définir la langue par défaut
     *
     * @param string $locale
     * @return void
     */
    public function setDefaultLocale(string $locale): void
    {
        if (isset($this->availableLocales[$locale])) {
            $this->defaultLocale = $locale;
        }
    }

    /**
     * Obtenir la langue par défaut
     *
     * @return string
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * Charger les traductions pour une langue
     *
     * @param string $locale
     * @return array
     */
    public function loadTranslations(string $locale): array
    {
        $cacheKey = "translations.{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($locale) {
            $path = $this->translationsPath . "/{$locale}.json";
            if (File::exists($path)) {
                return json_decode(File::get($path), true) ?? [];
            }
            return [];
        });
    }

    /**
     * Sauvegarder les traductions pour une langue
     *
     * @param string $locale
     * @param array $translations
     * @return bool
     */
    public function saveTranslations(string $locale, array $translations): bool
    {
        try {
            if (!File::exists($this->translationsPath)) {
                File::makeDirectory($this->translationsPath, 0755, true);
            }

            $path = $this->translationsPath . "/{$locale}.json";
            File::put($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            Cache::forget("translations.{$locale}");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Traduire une clé dans une langue spécifique
     *
     * @param string $key
     * @param string $locale
     * @param array $replace
     * @return string
     */
    public function translate(string $key, string $locale, array $replace = []): string
    {
        $translations = $this->loadTranslations($locale);
        $translation = $translations[$key] ?? $key;

        foreach ($replace as $key => $value) {
            $translation = str_replace(":$key", $value, $translation);
        }

        return $translation;
    }

    /**
     * Importer des traductions depuis un fichier
     *
     * @param string $locale
     * @param string $filePath
     * @return bool
     */
    public function importTranslations(string $locale, string $filePath): bool
    {
        try {
            if (!File::exists($filePath)) {
                return false;
            }

            $content = File::get($filePath);
            $translations = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }

            return $this->saveTranslations($locale, $translations);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Exporter les traductions vers un fichier
     *
     * @param string $locale
     * @param string $filePath
     * @return bool
     */
    public function exportTranslations(string $locale, string $filePath): bool
    {
        try {
            $translations = $this->loadTranslations($locale);
            File::put($filePath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}