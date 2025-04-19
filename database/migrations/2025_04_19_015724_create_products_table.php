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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('admin_id');
            $table->string('version')->default('1.0.0');
            $table->boolean('is_active')->default(true);
            $table->integer('max_activations_per_licence')->nullable();
            $table->integer('licence_duration_days')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Relation avec l'administrateur
            // Utilisation de unsignedBigInteger au lieu de foreignId pour éviter les problèmes de contraintes
            // $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
