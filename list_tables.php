<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Liste des tables dans la base de données :\n";
    
    $tables = DB::select('SHOW TABLES');
    $tableCount = count($tables);
    
    echo "Nombre total de tables : {$tableCount}\n\n";
    
    $i = 1;
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "{$i}. {$tableName}\n";
        $i++;
    }
    
    // Liste des tables qui devraient être présentes
    $expectedTables = [
        'admins',
        'admin_password_reset_tokens',
        'admin_role',
        'api_keys',
        'cache',
        'clients',
        'email_templates',
        'email_variables',
        'failed_jobs',
        'invoices',
        'invoice_items',
        'jobs',
        'licences',
        'licence_histories',
        'mail_configs',
        'migrations',
        'notifications',
        'password_reset_tokens',
        'payment_methods',
        'personal_access_tokens',
        'plans',
        'projects',
        'roles',
        'role_permissions',
        'serial_keys',
        'serial_key_histories',
        'sessions',
        'subscriptions',
        'support_tickets',
        'tenants',
        'ticket_replies',
        'users',
    ];
    
    echo "\nTables attendues mais manquantes :\n";
    $missingTables = [];
    foreach ($expectedTables as $expectedTable) {
        if (!Schema::hasTable($expectedTable)) {
            $missingTables[] = $expectedTable;
            echo "- {$expectedTable}\n";
        }
    }
    
    if (empty($missingTables)) {
        echo "Aucune table manquante parmi les tables attendues.\n";
    } else {
        echo "\nNombre de tables manquantes : " . count($missingTables) . "\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
