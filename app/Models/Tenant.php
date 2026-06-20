<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $connection = 'servit';

    protected $fillable = [
        'name', 'slug', 'status', 'plan_id', 'invitation_id',
        'db_name', 'db_host',
        'stripe_customer_id', 'stripe_connect_account_id',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class)->on('servit');
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class)->on('servit');
    }

    public function addOns(): HasMany
    {
        return $this->hasMany(TenantAddOn::class)->on('servit');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class)->on('servit');
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && $this->trial_ends_at?->isFuture();
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['trialing', 'active']);
    }
}
