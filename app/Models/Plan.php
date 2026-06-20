<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $connection = 'servit';

    protected $fillable = [
        'name', 'slug', 'stripe_product_id', 'stripe_price_id',
        'order_limit', 'rate_limit_per_hour', 'locations_included',
        'platform_fee_percent', 'price_monthly_cents', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_limit' => 'integer',
        'rate_limit_per_hour' => 'integer',
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function getPriceMonthlyAttribute(): string
    {
        return '$' . number_format($this->price_monthly_cents / 100, 2);
    }
}
