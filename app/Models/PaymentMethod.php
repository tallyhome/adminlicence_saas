<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'type',
        'provider',
        'provider_id',
        'card_brand',
        'card_last_four',
        'paypal_email',
        'is_default',
        'expires_at',
        'billing_details',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'expires_at' => 'datetime',
        'billing_details' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Payment method providers
     */
    const PROVIDER_STRIPE = 'stripe';
    const PROVIDER_PAYPAL = 'paypal';

    /**
     * Payment method types
     */
    const TYPE_CARD = 'card';
    const TYPE_PAYPAL = 'paypal';
    const TYPE_BANK_ACCOUNT = 'bank_account';

    /**
     * Get the tenant that owns the payment method.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the subscriptions that use this payment method.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scope a query to only include Stripe payment methods.
     */
    public function scopeStripe($query)
    {
        return $query->where('provider', self::PROVIDER_STRIPE);
    }

    /**
     * Scope a query to only include PayPal payment methods.
     */
    public function scopePaypal($query)
    {
        return $query->where('provider', self::PROVIDER_PAYPAL);
    }

    /**
     * Scope a query to only include default payment methods.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Check if the payment method is a card.
     */
    public function isCard()
    {
        return $this->type === self::TYPE_CARD;
    }

    /**
     * Check if the payment method is PayPal.
     */
    public function isPaypal()
    {
        return $this->type === self::TYPE_PAYPAL;
    }

    /**
     * Get a displayable representation of the payment method.
     */
    public function getDisplayName()
    {
        if ($this->isCard()) {
            return ucfirst($this->card_brand) . ' •••• ' . $this->card_last_four;
        }
        
        if ($this->isPaypal()) {
            return 'PayPal (' . $this->paypal_email . ')';
        }
        
        return 'Payment Method';
    }
}