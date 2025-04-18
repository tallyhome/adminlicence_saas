<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;

try {
    echo "Tentative de réparation de la base de données...\n";
    
    // Vérifier si la table migrations existe
    if (!Schema::hasTable('migrations')) {
        echo "La table 'migrations' n'existe pas. Création...\n";
        Schema::create('migrations', function (Blueprint $table) {
            $table->id();
            $table->string('migration');
            $table->integer('batch');
        });
        echo "Table 'migrations' créée.\n";
    }
    
    // Vérifier si la table sessions existe
    if (!Schema::hasTable('sessions')) {
        echo "Création de la table 'sessions'...\n";
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity')->index();
        });
        echo "Table 'sessions' créée avec succès.\n";
    } else {
        echo "La table 'sessions' existe déjà.\n";
    }
    
    // Vérifier si la table personal_access_tokens existe
    if (!Schema::hasTable('personal_access_tokens')) {
        echo "Création de la table 'personal_access_tokens'...\n";
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        echo "Table 'personal_access_tokens' créée avec succès.\n";
    }
    
    // Vérifier si la table products existe
    if (!Schema::hasTable('products')) {
        echo "Création de la table 'products'...\n";
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        echo "Table 'products' créée avec succès.\n";
    }
    
    // Vérifier si la table licenses existe
    if (!Schema::hasTable('licenses')) {
        echo "Création de la table 'licenses'...\n";
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('license_key')->unique();
            $table->date('expiration_date')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        echo "Table 'licenses' créée avec succès.\n";
    }
    
    // Exécuter la migration pour la table notifications
    if (Schema::hasTable('notifications')) {
        echo "La table 'notifications' existe déjà. Vérification des colonnes...\n";
        
        // Vérifier et ajouter les colonnes manquantes
        if (!Schema::hasColumn('notifications', 'read_at')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('target_ids');
            });
            echo "Colonne 'read_at' ajoutée à la table 'notifications'.\n";
        }
        
        if (!Schema::hasColumn('notifications', 'data')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->json('data')->nullable()->after('importance');
            });
            echo "Colonne 'data' ajoutée à la table 'notifications'.\n";
        }
    }
    
    echo "\nRéparation terminée. Veuillez rafraîchir votre application.\n";
    echo "Si des problèmes persistent, vous pouvez essayer d'exécuter 'php artisan migrate' manuellement.\n";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
