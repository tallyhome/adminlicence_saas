<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Tentative de correction de la migration pour la colonne read_by...\n";
    
    // Ajouter la migration pour éviter que Laravel ne tente d'ajouter la colonne read_by
    $migrationExists = DB::table('migrations')
        ->where('migration', '2025_04_17_031500_add_read_by_to_notifications_table')
        ->exists();
    
    if (!$migrationExists) {
        echo "Ajout de l'entrée de migration pour éviter que Laravel ne tente d'ajouter la colonne read_by...\n";
        DB::table('migrations')->insert([
            'migration' => '2025_04_17_031500_add_read_by_to_notifications_table',
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
