<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Tentative de correction des migrations de notifications...\n";
    
    // Trouver toutes les migrations liées aux notifications
    $notificationMigrations = DB::table('migrations')
        ->where('migration', 'like', '%notifications%')
        ->get();
    
    echo "Migrations liées aux notifications trouvées : " . $notificationMigrations->count() . "\n";
    
    foreach ($notificationMigrations as $migration) {
        echo "- {$migration->migration}\n";
    }
    
    // Ajouter toutes les migrations manquantes liées aux notifications
    $migrationsToAdd = [
        '2025_04_17_025200_create_notifications_table'
    ];
    
    foreach ($migrationsToAdd as $migration) {
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if (!$exists) {
            echo "Ajout de la migration {$migration}...\n";
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => 1
            ]);
            echo "Migration {$migration} ajoutée avec succès.\n";
        } else {
            echo "La migration {$migration} existe déjà.\n";
        }
    }
    
    echo "\nCorrection terminée. Vous pouvez maintenant exécuter 'php artisan migrate' sans erreur.\n";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
