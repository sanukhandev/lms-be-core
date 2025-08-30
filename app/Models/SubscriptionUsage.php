<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionUsage extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'quota_name',
        'current_usage',
        'last_updated_at',
    ];

    protected $casts = [
        'current_usage' => 'integer',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the subscription that owns this usage record.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get usage percentage based on quota limit.
     */
    public function getUsagePercentageAttribute(): float
    {
        $limit = $this->subscription->package->getQuotaLimit($this->quota_name);
        
        if ($limit === null || $limit === -1) {
            return 0; // Unlimited
        }

        if ($limit === 0) {
            return 100; // No quota allowed
        }

        return min(($this->current_usage / $limit) * 100, 100);
    }

    /**
     * Check if usage is near limit (>= 80%).
     */
    public function isNearLimit(): bool
    {
        return $this->getUsagePercentageAttribute() >= 80;
    }

    /**
     * Check if usage exceeds limit.
     */
    public function isExceeded(): bool
    {
        return $this->getUsagePercentageAttribute() >= 100;
    }

    /**
     * Get remaining quota.
     */
    public function getRemainingQuotaAttribute(): int
    {
        $limit = $this->subscription->package->getQuotaLimit($this->quota_name);
        
        if ($limit === null || $limit === -1) {
            return PHP_INT_MAX; // Unlimited
        }

        return max($limit - $this->current_usage, 0);
    }
}
