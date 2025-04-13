<?php
/**
 * Système de gestion des langues pour l'installateur
 */

// Définir les langues disponibles
define('AVAILABLE_LANGUAGES', ['fr', 'en']);
define('DEFAULT_LANGUAGE', 'fr');

// Initialiser la langue
function initLanguage() {
    // Vérifier si une langue est déjà définie en session
    session_start();
    
    if (isset($_POST['language']) && in_array($_POST['language'], AVAILABLE_LANGUAGES)) {
        $_SESSION['installer_language'] = $_POST['language'];
    } elseif (!isset($_SESSION['installer_language'])) {
        // Détecter la langue du navigateur
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr', 0, 2);
        $_SESSION['installer_language'] = in_array($browserLang, AVAILABLE_LANGUAGES) ? $browserLang : DEFAULT_LANGUAGE;
    }
    
    return $_SESSION['installer_language'];
}

// Charger les traductions
function loadTranslations($lang) {
    $langFile = __DIR__ . '/languages/' . $lang . '.php';
    
    if (file_exists($langFile)) {
        return include $langFile;
    }
    
    // Fallback sur la langue par défaut
    $defaultLangFile = __DIR__ . '/languages/' . DEFAULT_LANGUAGE . '.php';
    if (file_exists($defaultLangFile)) {
        return include $defaultLangFile;
    }
    
    // Si aucun fichier de langue n'est trouvé, retourner un tableau vide
    return [];
}

// Fonction de traduction
function t($key, $replacements = []) {
    static $translations = null;
    
    if ($translations === null) {
        $lang = initLanguage();
        $translations = loadTranslations($lang);
    }
    
    $text = $translations[$key] ?? $key;
    
    // Appliquer les remplacements
    if (!empty($replacements)) {
        foreach ($replacements as $placeholder => $value) {
            $text = str_replace(':' . $placeholder, $value, $text);
        }
    }
    
    return $text;
}

// Obtenir la langue actuelle
function getCurrentLanguage() {
    session_start();
    return $_SESSION['installer_language'] ?? DEFAULT_LANGUAGE;
}

// Obtenir les langues disponibles avec leurs noms
function getAvailableLanguages() {
    $languages = [];
    
    foreach (AVAILABLE_LANGUAGES as $lang) {
        $langName = '';
        switch ($lang) {
            case 'fr':
                $langName = 'Français';
                break;
            case 'en':
                $langName = 'English';
                break;
            default:
                $langName = ucfirst($lang);
        }
        
        $languages[$lang] = $langName;
    }
    
    return $languages;
}