<?php

$key = 'base64:' . base64_encode(random_bytes(32));
$envFile = __DIR__ . '/.env';

if (file_exists($envFile)) {
    $content = file_get_contents($envFile);
    
    // Remplacer ou ajouter APP_KEY
    if (preg_match('/^APP_KEY=.*$/m', $content)) {
        $content = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $content);
    } else {
        $content .= "\nAPP_KEY=" . $key;
    }
    
    file_put_contents($envFile, $content);
    echo "Clé d'application générée et ajoutée au fichier .env\n";
} else {
    echo "Le fichier .env n'existe pas\n";
} 