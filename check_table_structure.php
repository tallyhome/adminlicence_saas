<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the structure of the projects table
$columns = DB::select('SHOW COLUMNS FROM projects');

echo "Structure of the 'projects' table:\n";
echo "--------------------------------\n";
foreach ($columns as $column) {
    echo $column->Field . " - " . $column->Type;
    if ($column->Null === 'NO') {
        echo " (NOT NULL)";
    }
    if ($column->Default !== null) {
        echo " (Default: " . $column->Default . ")";
    }
    if ($column->Key === 'PRI') {
        echo " (PRIMARY KEY)";
    }
    echo "\n";
}
