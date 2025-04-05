<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serial_key_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_key_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->text('details')->nullable();
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->string('ip_address');
            $table->string('user_agent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serial_key_histories');
    }
}; 