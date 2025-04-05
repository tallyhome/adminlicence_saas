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
        Schema::table('serial_key_histories', function (Blueprint $table) {
            // Supprimer d'abord la contrainte de clé étrangère
            $table->dropForeign(['admin_id']);
            
            // Rendre la colonne nullable
            $table->foreignId('admin_id')->nullable()->change();
            
            // Réajouter la contrainte de clé étrangère, mais avec nullable
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serial_key_histories', function (Blueprint $table) {
            // Supprimer d'abord la contrainte de clé étrangère
            $table->dropForeign(['admin_id']);
            
            // Rendre la colonne non nullable à nouveau
            $table->foreignId('admin_id')->nullable(false)->change();
            
            // Réajouter la contrainte de clé étrangère
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }
};
