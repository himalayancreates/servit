<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AddOn extends Model
{
    protected $connection = 'servit';
    protected $table = 'add_ons';

    protected $fillable = [
        'name', 'slug', 'stripe_product_id', 'stripe_price_id',
        'billing_type', 'price_cents', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_add_ons')
            ->withPivot(['quantity', 'stripe_subscription_item_id', 'activated_at', 'deactivated_at'])
            ->withTimestamps();
    }

    public function getPriceAttribute(): string
    {
        return '$' . number_format($this->price_cents / 100, 2);
    }
}
