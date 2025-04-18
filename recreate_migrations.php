<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;

try {
    echo "Création des migrations manquantes...\n";
    
    // Liste des migrations à créer
    $migrations = [
        '2024_05_20_000001_create_admin_role_table' => [
            'type' => 'create',
            'table' => 'admin_role',
            'callback' => function (Blueprint $table) {
                $table->unsignedBigInteger('admin_id');
                $table->unsignedBigInteger('role_id');
                $table->primary(['admin_id', 'role_id']);
                $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->timestamps();
            }
        ],
        '2025_04_02_160003_create_personal_access_tokens_table' => [
            'type' => 'create',
            'table' => 'personal_access_tokens',
            'callback' => function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            }
        ],
        '2025_04_03_054922_make_admin_id_nullable_in_serial_key_histories_table' => [
            'type' => 'modify',
            'table' => 'serial_key_histories',
            'callback' => function (Blueprint $table) {
                $table->unsignedBigInteger('admin_id')->nullable()->change();
            }
        ],
        '2025_04_07_072637_create_admin_password_reset_tokens_table' => [
            'type' => 'create',
            'table' => 'admin_password_reset_tokens',
            'callback' => function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            }
        ],
        '2025_04_07_094844_add_content_column_to_email_templates' => [
            'type' => 'modify',
            'table' => 'email_templates',
            'callback' => function (Blueprint $table) {
                $table->text('content')->nullable()->after('subject');
            }
        ],
        '2025_04_07_110500_add_is_system_column_to_email_templates' => [
            'type' => 'modify',
            'table' => 'email_templates',
            'callback' => function (Blueprint $table) {
                $table->boolean('is_system')->default(false)->after('content');
            }
        ],
        '2025_04_08_000001_add_is_super_admin_to_admins_table' => [
            'type' => 'modify',
            'table' => 'admins',
            'callback' => function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(false)->after('email');
            }
        ],
        '2025_04_11_223351_create_cache_table' => [
            'type' => 'create',
            'table' => 'cache',
            'callback' => function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            }
        ],
        '2025_04_17_030200_add_admin_id_to_users_table' => [
            'type' => 'modify',
            'table' => 'users',
            'callback' => function (Blueprint $table) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('id');
                $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            }
        ]
    ];
    
    // Créer chaque migration si elle n'existe pas déjà
    foreach ($migrations as $name => $config) {
        $migrationExists = DB::table('migrations')->where('migration', $name)->exists();
        
        if (!$migrationExists) {
            echo "Création de la migration {$name}...\n";
            
            if ($config['type'] === 'create' && !Schema::hasTable($config['table'])) {
                Schema::create($config['table'], $config['callback']);
                DB::table('migrations')->insert([
                    'migration' => $name,
                    'batch' => 1
                ]);
                echo "Table {$config['table']} créée avec succès.\n";
            } elseif ($config['type'] === 'modify' && Schema::hasTable($config['table'])) {
                Schema::table($config['table'], $config['callback']);
                DB::table('migrations')->insert([
                    'migration' => $name,
                    'batch' => 1
                ]);
                echo "Table {$config['table']} modifiée avec succès.\n";
            } else {
                echo "La table {$config['table']} n'existe pas ou existe déjà, migration ignorée.\n";
            }
        } else {
            echo "La migration {$name} existe déjà.\n";
        }
    }
    
    echo "\nToutes les migrations ont été créées avec succès.\n";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
