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
        Schema::create('serial_keys', function (Blueprint $table) {
            $table->id();
            $table->string('serial_key')->unique();
            $table->enum('status', ['active', 'revoked', 'expired', 'suspended'])->default('active');
            $table->unsignedBigInteger('project_id');
            $table->string('domain')->nullable();
            $table->string('ip_address')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serial_keys');
    }
};