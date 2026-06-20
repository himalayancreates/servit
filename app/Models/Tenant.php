<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    protected $connection = 'servit';

    protected $fillable = [
        'name', 'slug', 'status', 'plan_id', 'invitation_id',
        'db_name', 'db_host', 'db_provisioned',
        'stripe_customer_id', 'stripe_connect_account_id',
        'overage_fee_per_order_cents', 'notes',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
