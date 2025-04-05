<?php
/**
 * Script pour nettoyer manuellement tous les caches de Laravel 12
 * 
 * Ce script remplace les commandes comme "php artisan optimize:clear" et "php artisan cache:clear"
 * qui ne sont plus disponibles dans Laravel 12 par défaut.
 */

// Vérifier que le script est exécuté en ligne de commande
if (php_sapi_name() !== 'cli') {
    die("Ce script doit être exécuté en ligne de commande.");
}

echo "Début du nettoyage des caches Laravel...\n";

// Liste des dossiers à nettoyer
$cacheDirs = [
    __DIR__ . '/bootstrap/cache',
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/storage/framework/views',
    __DIR__ . '/storage/framework/sessions',
    __DIR__ . '/storage/logs',
];

// Fonction pour vider un dossier sans le supprimer
function emptyDirectory($dir) {
    if (!is_dir($dir)) {
        echo "Création du dossier $dir...\n";
        mkdir($dir, 0755, true);
        return;
    }
    
    echo "Nettoyage du dossier $dir...\n";
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($files as $fileinfo) {
        if ($fileinfo->isDir()) {
            // Conserver les dossiers .gitignore
            if ($fileinfo->getFilename() !== '.git') {
                rmdir($fileinfo->getRealPath());
            }
        } else {
            // Conserver les fichiers .gitignore
            if ($fileinfo->getFilename() !== '.gitignore') {
                unlink($fileinfo->getRealPath());
            }
        }
    }
}

// Nettoyer chaque dossier
foreach ($cacheDirs as $dir) {
    emptyDirectory($dir);
}

// Recréer les fichiers de configuration compilés
echo "Recréation des fichiers de configuration compilés...\n";

// Nettoyer le fichier de routes compilées
$routesCache = __DIR__ . '/bootstrap/cache/routes.php';
if (file_exists($routesCache)) {
    unlink($routesCache);
    echo "Fichier de routes compilées supprimé.\n";
}

// Nettoyer le fichier de services compilés
$servicesCache = __DIR__ . '/bootstrap/cache/services.php';
if (file_exists($servicesCache)) {
    unlink($servicesCache);
    echo "Fichier de services compilés supprimé.\n";
}

// Nettoyer le fichier de packages compilés
$packagesCache = __DIR__ . '/bootstrap/cache/packages.php';
if (file_exists($packagesCache)) {
    unlink($packagesCache);
    echo "Fichier de packages compilés supprimé.\n";
}

// Nettoyer le fichier de configuration compilé
$configCache = __DIR__ . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    echo "Fichier de configuration compilé supprimé.\n";
}

// Nettoyer le fichier d'événements compilés
$eventsCache = __DIR__ . '/bootstrap/cache/events.php';
if (file_exists($eventsCache)) {
    unlink($eventsCache);
    echo "Fichier d'événements compilé supprimé.\n";
}

echo "\nNettoyage des caches terminé. L'application devrait maintenant utiliser les fichiers de configuration à jour.\n";
