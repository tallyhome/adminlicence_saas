<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Tentative de correction de toutes les migrations problématiques...\n";
    
    // Liste de toutes les migrations problématiques à marquer comme exécutées
    $migrationsToAdd = [
        '2025_04_17_031800_create_notifications_table',
        '2025_04_17_164526_update_notifications_table_structure'
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
    
    // Vérifier si la table notifications a toutes les colonnes nécessaires
    if (Schema::hasTable('notifications')) {
        echo "\nVérification des colonnes de la table notifications...\n";
        
        $columnsToCheck = [
            'title' => 'string',
            'message' => 'text',
            'sender_id' => 'unsignedBigInteger',
            'sender_type' => 'string',
            'target_type' => 'string',
            'target_ids' => 'json',
            'read_at' => 'timestamp',
            'read' => 'boolean',
            'read_by' => 'json',
            'importance' => 'string',
            'data' => 'json'
        ];
        
        foreach ($columnsToCheck as $column => $type) {
            if (!Schema::hasColumn('notifications', $column)) {
                echo "La colonne '{$column}' est manquante. Ajout...\n";
                Schema::table('notifications', function (Blueprint $table) use ($column, $type) {
                    switch ($type) {
                        case 'string':
                            $table->string($column)->nullable();
                            break;
                        case 'text':
                            $table->text($column)->nullable();
                            break;
                        case 'unsignedBigInteger':
                            $table->unsignedBigInteger($column)->nullable();
                            break;
                        case 'json':
                            $table->json($column)->nullable();
                            break;
                        case 'boolean':
                            $table->boolean($column)->default(false);
                            break;
                        case 'timestamp':
                            $table->timestamp($column)->nullable();
                            break;
                    }
                });
                echo "Colonne '{$column}' ajoutée avec succès.\n";
            } else {
                echo "La colonne '{$column}' existe déjà.\n";
            }
        }
    }
    
    echo "\nCorrection terminée. Vous pouvez maintenant exécuter 'php artisan migrate' sans erreur.\n";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
