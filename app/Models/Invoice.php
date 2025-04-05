<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'provider',
        'provider_id',
        'number',
        'total',
        'currency',
        'status',
        'billing_reason',
        'billing_details',
        'payment_method_id',
        'payment_method_type',
        'paid_at',
        'due_at',
        'refunded_at',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total' => 'float',
        'billing_details' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'due_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Invoice providers
     */
    const PROVIDER_STRIPE = 'stripe';
    const PROVIDER_PAYPAL = 'paypal';
    const PROVIDER_MANUAL = 'manual';

    /**
     * Invoice statuses
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_OPEN = 'open';
    const STATUS_PAID = 'paid';
    const STATUS_UNCOLLECTIBLE = 'uncollectible';
    const STATUS_VOID = 'void';

    /**
     * Get the tenant that owns the invoice.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the subscription that owns the invoice.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the payment method used for the invoice.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the invoice items for the invoice.
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope a query to only include unpaid invoices.
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [self::STATUS_DRAFT, self::STATUS_OPEN]);
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OPEN)
                     ->where('due_at', '<', now());
    }

    /**
     * Check if the invoice is paid.
     */
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if the invoice is overdue.
     */
    public function isOverdue()
    {
        return $this->status === self::STATUS_OPEN && $this->due_at && $this->due_at->isPast();
    }

    /**
     * Get the formatted total amount.
     */
    public function formattedTotal()
    {
        return number_format($this->total / 100, 2) . ' ' . strtoupper($this->currency);
    }
}