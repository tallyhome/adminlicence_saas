<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',  // monthly, yearly
        'features',       // JSON array of features
        'is_active',
        'stripe_price_id',
        'paypal_plan_id',
        'trial_days',
        'max_licenses',
        'max_projects',
        'max_products',
        'max_product_licenses',
        'max_apis',
        'max_api_keys',
        'has_api_access'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'max_licenses' => 'integer',
        'max_projects' => 'integer',
        'max_products' => 'integer',
        'max_product_licenses' => 'integer',
        'max_apis' => 'integer',
        'max_api_keys' => 'integer',
        'has_api_access' => 'boolean'
    ];

    /**
     * Get the subscriptions for the plan
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scope a query to only include active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the plan is free
     */
    public function isFree(): bool
    {
        return $this->price == 0;
    }

    /**
     * Check if the plan has a trial period
     */
    public function hasTrial(): bool
    {
        return $this->trial_days > 0;
    }
}