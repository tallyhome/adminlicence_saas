<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Supprimer la colonne max_clients si elle existe
            if (Schema::hasColumn('plans', 'max_clients')) {
                $table->dropColumn('max_clients');
            }
            
            // Ajouter les nouvelles colonnes pour les limites de ressources
            $table->integer('max_products')->default(5)->after('max_projects');
            $table->integer('max_product_licenses')->default(10)->after('max_products');
            $table->integer('max_apis')->default(0)->after('max_product_licenses');
            $table->integer('max_api_keys')->default(0)->after('max_apis');
            $table->boolean('has_api_access')->default(false)->after('max_api_keys');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Supprimer les nouvelles colonnes
            $table->dropColumn([
                'max_products',
                'max_product_licenses',
                'max_apis',
                'max_api_keys',
                'has_api_access'
            ]);
            
            // Rétablir la colonne max_clients
            $table->integer('max_clients')->default(10)->after('max_projects');
        });
    }
};
