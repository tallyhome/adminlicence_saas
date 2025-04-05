<?php
/**
 * Script de réparation pour l'application Laravel
 * Ce script tente de corriger les problèmes de configuration qui empêchent
 * l'application de démarrer correctement.
 */

// Vérifier que le script est exécuté en ligne de commande
if (php_sapi_name() !== 'cli') {
    die("Ce script doit être exécuté en ligne de commande.");
}

echo "Début de la réparation de l'application Laravel...\n";

// Étape 1: Vérifier le fichier de configuration app.php
$appConfigPath = __DIR__ . '/config/app.php';
if (!file_exists($appConfigPath)) {
    die("Fichier de configuration app.php introuvable.\n");
}

echo "Vérification du fichier app.php...\n";

// Lire le contenu du fichier
$appConfig = file_get_contents($appConfigPath);

// Créer une sauvegarde
file_put_contents($appConfigPath . '.backup', $appConfig);
echo "Sauvegarde créée: config/app.php.backup\n";

// Vérifier les providers
if (strpos($appConfig, 'files') !== false) {
    echo "Référence problématique 'files' trouvée dans app.php\n";
    
    // Remplacer la référence problématique
    $appConfig = str_replace("'files'", "// 'files' - commenté pour résoudre le problème", $appConfig);
    file_put_contents($appConfigPath, $appConfig);
    echo "Référence 'files' commentée dans app.php\n";
}

// Étape 2: Nettoyer le cache de configuration
echo "Nettoyage manuel du cache de configuration...\n";

$configCachePath = __DIR__ . '/bootstrap/cache/config.php';
if (file_exists($configCachePath)) {
    if (unlink($configCachePath)) {
        echo "Cache de configuration supprimé avec succès.\n";
    } else {
        echo "Impossible de supprimer le cache de configuration.\n";
    }
} else {
    echo "Aucun cache de configuration trouvé.\n";
}

// Étape 3: Nettoyer le cache des routes
echo "Nettoyage manuel du cache des routes...\n";

$routeCachePath = __DIR__ . '/bootstrap/cache/routes.php';
if (file_exists($routeCachePath)) {
    if (unlink($routeCachePath)) {
        echo "Cache des routes supprimé avec succès.\n";
    } else {
        echo "Impossible de supprimer le cache des routes.\n";
    }
} else {
    echo "Aucun cache de routes trouvé.\n";
}

// Étape 4: Vérifier les autres fichiers de cache
echo "Nettoyage des autres fichiers de cache...\n";

$cacheDir = __DIR__ . '/bootstrap/cache/';
$cacheFiles = glob($cacheDir . '*.php');
foreach ($cacheFiles as $file) {
    echo "Suppression de " . basename($file) . "...\n";
    unlink($file);
}

// Étape 5: Vérifier le fichier .env
echo "Vérification du fichier .env...\n";

$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "Fichier .env introuvable. Création à partir de .env.example...\n";
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', $envPath);
        echo "Fichier .env créé avec succès.\n";
    } else {
        echo "Fichier .env.example introuvable. Impossible de créer .env.\n";
    }
}

// Étape 6: Vérifier les permissions du dossier storage
echo "Vérification des permissions du dossier storage...\n";

$storagePath = __DIR__ . '/storage';
if (is_dir($storagePath)) {
    echo "Dossier storage trouvé.\n";
    
    // Vérifier les sous-dossiers
    $subDirs = ['logs', 'framework/cache', 'framework/sessions', 'framework/views'];
    foreach ($subDirs as $dir) {
        $path = $storagePath . '/' . $dir;
        if (!is_dir($path)) {
            echo "Création du dossier $path...\n";
            mkdir($path, 0755, true);
        }
    }
    
    echo "Structure du dossier storage vérifiée.\n";
} else {
    echo "Dossier storage introuvable. Cela peut causer des problèmes.\n";
}

echo "\nRéparation terminée. Essayez maintenant d'exécuter 'php artisan serve' pour voir si le problème est résolu.\n";
echo "Si le problème persiste, vous pouvez toujours accéder à la documentation via http://votre-domaine.com/api-docs.php\n";
