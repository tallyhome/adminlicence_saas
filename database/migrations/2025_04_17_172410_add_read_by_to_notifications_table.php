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
            // Vérifier si la colonne read_by n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'read_by')) {
                $table->json('read_by')->nullable()->after('read_at');
            }
            
            // Vérifier si la colonne importance n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'importance')) {
                $table->string('importance')->default('normal')->after('read_by');
            }
            
            // Vérifier si la colonne title n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            
            // Vérifier si la colonne message n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable()->after('title');
            }
            
            // Vérifier si la colonne sender_id n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'sender_id')) {
                $table->unsignedBigInteger('sender_id')->nullable()->after('message');
            }
            
            // Vérifier si la colonne sender_type n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'sender_type')) {
                $table->string('sender_type')->default('admin')->after('sender_id');
            }
            
            // Vérifier si la colonne target_type n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'target_type')) {
                $table->string('target_type')->default('all')->after('sender_type');
            }
            
            // Vérifier si la colonne target_ids n'existe pas déjà
            if (!Schema::hasColumn('notifications', 'target_ids')) {
                $table->json('target_ids')->nullable()->after('target_type');
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
