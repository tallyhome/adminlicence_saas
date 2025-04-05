<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'description',
        'amount',
        'currency',
        'quantity',
        'period_start',
        'period_end',
        'type',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'float',
        'quantity' => 'integer',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Invoice item types
     */
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_INVOICE_ITEM = 'invoice_item';
    const TYPE_TAX = 'tax';
    const TYPE_DISCOUNT = 'discount';

    /**
     * Get the invoice that owns the invoice item.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the total amount for this item (amount * quantity).
     */
    public function total()
    {
        return $this->amount * $this->quantity;
    }

    /**
     * Get the formatted amount.
     */
    public function formattedAmount()
    {
        return number_format($this->amount / 100, 2) . ' ' . strtoupper($this->currency);
    }

    /**
     * Get the formatted total amount.
     */
    public function formattedTotal()
    {
        return number_format($this->total() / 100, 2) . ' ' . strtoupper($this->currency);
    }
}