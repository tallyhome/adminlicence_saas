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
            $table->foreignId('licence_id')->constrained()->onDelete('cascade');
            $table->string('device_id');
            $table->string('device_name');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('activated_at');
            $table->timestamp('deactivated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Index pour accélérer les recherches par device_id
            $table->index('device_id');
            // Index composé pour vérifier rapidement les activations uniques
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
