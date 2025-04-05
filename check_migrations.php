<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$migrations = DB::table('migrations')->get();

echo "Migrations that have been run:\n";
echo "-----------------------------\n";
foreach ($migrations as $migration) {
    echo $migration->migration . " (Batch: " . $migration->batch . ")\n";
}

echo "\n";
echo "Pending migrations:\n";
echo "-----------------\n";

$files = glob(__DIR__ . '/database/migrations/*.php');
$migrated = $migrations->pluck('migration')->toArray();

foreach ($files as $file) {
    $filename = basename($file, '.php');
    if (!in_array($filename, $migrated)) {
        echo $filename . "\n";
    }
}
