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
        // Ne créer la table que si elle n'existe pas déjà
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                
                // Colonnes personnalisées pour notre système de notifications
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->string('sender_type')->default('admin');
                $table->string('target_type')->default('all'); // all, admins, users, specific
                $table->json('target_ids')->nullable();
                $table->boolean('read')->default(false);
                $table->json('read_by')->nullable();
                $table->string('importance')->default('normal');
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas supprimer la table pour éviter de perdre des données importantes
        // Si vous voulez vraiment supprimer la table, décommentez la ligne ci-dessous
        // Schema::dropIfExists('notifications');
    }
};
