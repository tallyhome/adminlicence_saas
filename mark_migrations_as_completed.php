<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Migrations to mark as completed
$migrations = [
    '2024_03_21_000000_create_admins_table',
    '2024_03_21_000002_create_projects_table',
    '2024_03_21_000003_create_serial_keys_table',
    '2024_03_21_000004_add_status_to_projects_table',
    '2025_04_03_044638_ensure_status_column_in_projects_table'
];

// Get the latest batch number
$latestBatch = DB::table('migrations')->max('batch');
$newBatch = $latestBatch + 1;

// Insert migrations as completed
$inserted = 0;
foreach ($migrations as $migration) {
    // Check if migration already exists
    $exists = DB::table('migrations')->where('migration', $migration)->exists();
    
    if (!$exists) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $newBatch
        ]);
        echo "Marked migration as completed: {$migration}\n";
        $inserted++;
    } else {
        echo "Migration already exists: {$migration}\n";
    }
}

echo "\nTotal migrations marked as completed: {$inserted}\n";
echo "All done! Your migrations are now in sync with your database structure.\n";
