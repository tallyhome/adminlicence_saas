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
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('plan_id')->constrained()->onDelete('cascade');
                $table->string('status')->default('active'); // active, canceled, expired, trial
                $table->timestamp('starts_at')->useCurrent();
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->string('stripe_subscription_id')->nullable();
                $table->string('paypal_subscription_id')->nullable();
                $table->string('payment_method')->nullable(); // stripe, paypal
                $table->decimal('renewal_price', 10, 2)->nullable();
                $table->string('billing_cycle')->nullable(); // monthly, yearly
                $table->boolean('auto_renew')->default(true);
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index('stripe_subscription_id');
                $table->index('paypal_subscription_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};