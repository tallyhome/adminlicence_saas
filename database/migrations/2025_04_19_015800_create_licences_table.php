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
        Schema::create('licences', function (Blueprint $table) {
            $table->id();
            $table->string('licence_key')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->string('status')->default('active'); // active, expired, suspended, revoked
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_activations')->nullable();
            $table->integer('current_activations')->default(0);
            $table->timestamp('last_check_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Relations
            // Utilisation de unsignedBigInteger au lieu de foreignId pour éviter les problèmes de contraintes
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licences');
    }
};
