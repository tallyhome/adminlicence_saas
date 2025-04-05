<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    /**
     * Liste des langues disponibles dans l'application
     *
     * @var array
     */
    protected $availableLocales = [
        'en', // Anglais
        'fr', // Français
        'es', // Espagnol
        'de', // Allemand
        'it', // Italien
        'pt', // Portugais
        'nl', // Néerlandais
        'ru', // Russe
        'zh', // Chinois
        'ja', // Japonais
        'tr', // Turc
        'ar'  // Arabe
    ];

    /**
     * Obtenir la liste des langues disponibles
     *
     * @return array
     */
    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    /**
     * Vérifier si une langue est disponible
     *
     * @param string $locale
     * @return bool
     */
    public function isLocaleAvailable(string $locale): bool
    {
        return in_array($locale, $this->availableLocales);
    }

    /**
     * Définir la langue active
     *
     * @param string $locale
     * @return bool
     */
    public function setLocale(string $locale): bool
    {
        if ($this->isLocaleAvailable($locale)) {
            Session::put('locale', $locale);
            App::setLocale($locale);
            return true;
        }
        
        return false;
    }

    /**
     * Obtenir la langue active
     *
     * @return string
     */
    public function getLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Obtenir le nom de la langue dans sa langue native
     *
     * @param string $locale
     * @return string
     */
    public function getLocaleName(string $locale): string
    {
        $names = [
            'en' => 'English',
            'fr' => 'Français',
            'es' => 'Español',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'pt' => 'Português',
            'nl' => 'Nederlands',
            'ru' => 'Русский',
            'zh' => '中文',
            'ja' => '日本語',
            'tr' => 'Türkçe',
            'ar' => 'العربية'
        ];

        return $names[$locale] ?? $locale;
    }

    /**
     * Obtenir les traductions pour la langue active
     *
     * @param string|null $locale
     * @return array
     */
    public function getTranslations(?string $locale = null): array
    {
        $locale = $locale ?? $this->getLocale();
        
        // Utiliser le cache pour améliorer les performances
        $cacheKey = 'translations_' . $locale;
        
        return Cache::remember($cacheKey, now()->addDay(), function () use ($locale) {
            $path = resource_path('locales/' . $locale . '/translation.json');
            
            if (File::exists($path)) {
                return json_decode(File::get($path), true) ?? [];
            }
            
            // Fallback vers l'anglais si la traduction n'existe pas
            $fallbackPath = resource_path('locales/en/translation.json');
            
            if (File::exists($fallbackPath)) {
                return json_decode(File::get($fallbackPath), true) ?? [];
            }
            
            return [];
        });
    }

    /**
     * Traduire une clé
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    public function translate(string $key, array $replace = [], ?string $locale = null): string
    {
        $translations = $this->getTranslations($locale);
        
        $keys = explode('.', $key);
        $value = $translations;
        
        foreach ($keys as $segment) {
            if (isset($value[$segment])) {
                $value = $value[$segment];
            } else {
                // Clé non trouvée, retourner la clé elle-même
                return $key;
            }
        }
        
        if (is_string($value)) {
            // Remplacer les variables dans la traduction
            foreach ($replace as $key => $replacement) {
                $value = str_replace(':' . $key, $replacement, $value);
            }
            
            return $value;
        }
        
        // Si la valeur n'est pas une chaîne, retourner la clé
        return $key;
    }
}