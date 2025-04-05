<?php
/**
 * Script de réparation pour Laravel 12
 * 
 * Ce script corrige les problèmes courants avec Laravel 12 qui empêchent
 * les commandes artisan de fonctionner correctement.
 */

// Vérifier que le script est exécuté en ligne de commande
if (php_sapi_name() !== 'cli') {
    die("Ce script doit être exécuté en ligne de commande.");
}

echo "Début de la réparation de l'application Laravel 12...\n";

// Étape 1: Vérifier le fichier bootstrap/app.php
$bootstrapPath = __DIR__ . '/bootstrap/app.php';
if (!file_exists($bootstrapPath)) {
    die("Fichier bootstrap/app.php introuvable.\n");
}

echo "Vérification du fichier bootstrap/app.php...\n";

// Lire le contenu du fichier
$bootstrapContent = file_get_contents($bootstrapPath);

// Créer une sauvegarde
file_put_contents($bootstrapPath . '.backup', $bootstrapContent);
echo "Sauvegarde créée: bootstrap/app.php.backup\n";

// Vérifier si le fichier contient la configuration pour les commandes
if (strpos($bootstrapContent, 'commands:') === false) {
    echo "Configuration des commandes manquante dans bootstrap/app.php\n";
    
    // Ajouter la configuration des commandes
    $bootstrapContent = str_replace(
        "->withRouting(",
        "->withCommands()
    ->withRouting(",
        $bootstrapContent
    );
    
    file_put_contents($bootstrapPath, $bootstrapContent);
    echo "Configuration des commandes ajoutée dans bootstrap/app.php\n";
}

// Étape 2: Vérifier le fichier artisan
$artisanPath = __DIR__ . '/artisan';
if (!file_exists($artisanPath)) {
    die("Fichier artisan introuvable.\n");
}

echo "Vérification du fichier artisan...\n";

// Lire le contenu du fichier
$artisanContent = file_get_contents($artisanPath);

// Créer une sauvegarde
file_put_contents($artisanPath . '.backup', $artisanContent);
echo "Sauvegarde créée: artisan.backup\n";

// Vérifier si le fichier artisan est correctement configuré
$correctArtisan = <<<'EOD'
#!/usr/bin/env php
<?php

use Illuminate\Foundation\Application;
use Symfony\Component\Console\Input\ArgvInput;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the command...
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate($input, $status);

exit($status);
EOD;

if ($artisanContent !== $correctArtisan) {
    echo "Le fichier artisan n'est pas correctement configuré. Mise à jour...\n";
    file_put_contents($artisanPath, $correctArtisan);
    echo "Fichier artisan mis à jour.\n";
}

// Étape 3: Vérifier le fichier composer.json
$composerPath = __DIR__ . '/composer.json';
if (!file_exists($composerPath)) {
    die("Fichier composer.json introuvable.\n");
}

echo "Vérification du fichier composer.json...\n";

// Lire le contenu du fichier
$composerContent = file_get_contents($composerPath);
$composerJson = json_decode($composerContent, true);

// Créer une sauvegarde
file_put_contents($composerPath . '.backup', $composerContent);
echo "Sauvegarde créée: composer.json.backup\n";

// Vérifier si les packages nécessaires sont présents
$requiredPackages = [
    "laravel/framework" => "^12.0",
    "laravel/tinker" => "^2.9",
];

$needsUpdate = false;
foreach ($requiredPackages as $package => $version) {
    if (!isset($composerJson['require'][$package])) {
        echo "Package $package manquant dans composer.json. Ajout...\n";
        $composerJson['require'][$package] = $version;
        $needsUpdate = true;
    }
}

// Vérifier si les scripts nécessaires sont présents
if (!isset($composerJson['scripts']['post-autoload-dump'])) {
    echo "Script post-autoload-dump manquant dans composer.json. Ajout...\n";
    $composerJson['scripts']['post-autoload-dump'] = [
        "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
        "@php artisan package:discover --ansi"
    ];
    $needsUpdate = true;
}

// Mettre à jour composer.json si nécessaire
if ($needsUpdate) {
    file_put_contents($composerPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "Fichier composer.json mis à jour.\n";
}

// Étape 4: Nettoyer le cache de l'application manuellement
echo "Nettoyage manuel du cache...\n";

$cacheDirs = [
    __DIR__ . '/bootstrap/cache',
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/storage/framework/views',
    __DIR__ . '/storage/framework/sessions',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        echo "Nettoyage du dossier $dir...\n";
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    } else {
        echo "Création du dossier $dir...\n";
        mkdir($dir, 0755, true);
    }
}

echo "\nRéparation terminée. Essayez maintenant d'exécuter 'php artisan' pour voir si le problème est résolu.\n";
echo "Si le problème persiste, vous devrez peut-être exécuter 'composer dump-autoload' ou 'composer update'.\n";
