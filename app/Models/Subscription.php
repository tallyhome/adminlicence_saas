<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'status',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at',
        'stripe_subscription_id',
        'paypal_subscription_id',
        'payment_method',
        'renewal_price',
        'billing_cycle',
        'auto_renew'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'renewal_price' => 'decimal:2',
        'auto_renew' => 'boolean'
    ];

    /**
     * Payment method types
     */
    const PAYMENT_METHOD_STRIPE = 'stripe';
    const PAYMENT_METHOD_PAYPAL = 'paypal';

    /**
     * Get the tenant that owns the subscription
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the plan that the subscription belongs to
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the payment method associated with the subscription.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the invoices for the subscription.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Check if the subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' || $this->onTrial();
    }

    /**
     * Check if the subscription is on trial
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }

    /**
     * Check if the subscription has expired
     */
    public function hasExpired(): bool
    {
        return $this->ends_at && now()->gte($this->ends_at);
    }

    /**
     * Check if the subscription is canceled
     */
    public function isCanceled(): bool
    {
        return $this->canceled_at !== null;
    }

    /**
     * Cancel the subscription
     */
    public function cancel(): void
    {
        $this->canceled_at = now();
        $this->auto_renew = false;
        $this->save();
    }

    /**
     * Resume the subscription
     */
    public function resume(): void
    {
        $this->canceled_at = null;
        $this->auto_renew = true;
        $this->save();
    }
}