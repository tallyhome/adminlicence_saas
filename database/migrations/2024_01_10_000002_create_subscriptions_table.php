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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained();
            $table->string('status')->default('active'); // active, canceled, expired, trial
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('paypal_subscription_id')->nullable();
            $table->string('payment_method')->nullable(); // stripe, paypal
            $table->decimal('renewal_price', 10, 2)->nullable();
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->boolean('auto_renew')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index('stripe_subscription_id');
            $table->index('paypal_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};