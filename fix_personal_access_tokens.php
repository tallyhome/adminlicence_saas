<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Tentative de correction de la table personal_access_tokens...\n";
    
    // Vérifier si la migration existe déjà dans la table migrations
    $migrationExists = DB::table('migrations')
        ->where('migration', '2025_04_02_160003_create_personal_access_tokens_table')
        ->exists();
    
    if (!$migrationExists) {
        echo "Ajout de l'entrée de migration pour éviter que Laravel ne tente de créer la table personal_access_tokens...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_04_02_160003_create_personal_access_tokens_table',
            'batch' => 1
        ]);
        echo "Entrée de migration ajoutée avec succès.\n";
    } else {
        echo "L'entrée de migration existe déjà.\n";
    }
    
    echo "\nCorrection terminée. Vous pouvez maintenant exécuter 'php artisan migrate' sans erreur.\n";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
