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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
