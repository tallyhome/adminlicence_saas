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
        'name',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'paypal_id',
        'paypal_status',
        'paypal_plan',
        'quantity',
        'trial_ends_at',
        'ends_at',
        'payment_method_id',
        'payment_method_type'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Payment method types
     */
    const PAYMENT_METHOD_STRIPE = 'stripe';
    const PAYMENT_METHOD_PAYPAL = 'paypal';

    /**
     * Get the tenant that owns the subscription.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
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
     * Determine if the subscription is active.
     */
    public function isActive()
    {
        return $this->stripe_status === 'active' || $this->onTrial();
    }

    /**
     * Determine if the subscription is on trial.
     */
    public function onTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Determine if the subscription is canceled.
     */
    public function canceled()
    {
        return $this->ends_at !== null;
    }

    /**
     * Determine if the subscription has ended.
     */
    public function ended()
    {
        return $this->canceled() && $this->ends_at->isPast();
    }
}