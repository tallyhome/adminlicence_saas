<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serial_keys', function (Blueprint $table) {
            $table->id();
            $table->string('serial_key')->unique();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'suspended', 'revoked', 'expired'])->default('active');
            $table->string('domain')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serial_keys');
    }
}; 