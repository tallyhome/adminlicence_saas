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
        Schema::table('notifications', function (Blueprint $table) {
            // Vérifier si la colonne data n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->json('data')->nullable()->after('importance');
            }
            
            // Vérifier si la colonne read n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'read')) {
                $table->boolean('read')->default(false)->after('target_ids');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire en cas de rollback pour éviter de perdre des données
    }
};
