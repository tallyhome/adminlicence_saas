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
        Schema::create('licence_activations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('licence_id');
            $table->string('device_id');
            $table->string('device_name')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('activated_at');
            $table->timestamp('deactivated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Relation avec la licence
            // Utilisation de unsignedBigInteger au lieu de foreignId pour éviter les problèmes de contraintes
            // $table->foreign('licence_id')->references('id')->on('licences')->onDelete('cascade');
            
            // Un appareil ne peut avoir qu'une seule activation active par licence
            $table->unique(['licence_id', 'device_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licence_activations');
    }
};
