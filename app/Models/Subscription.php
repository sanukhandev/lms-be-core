<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'package_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'status',
        'amount',
        'currency',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'expires_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the package for this subscription.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the usage records for this subscription.
     */
    public function usage(): HasMany
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for trial subscriptions.
     */
    public function scopeTrialing($query)
    {
        return $query->where('status', 'trialing')
                    ->where('trial_ends_at', '>', now());
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Check if subscription is on trial.
     */
    public function onTrial(): bool
    {
        return $this->status === 'trialing' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is past due.
     */
    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    /**
     * Get current usage for a quota.
     */
    public function getCurrentUsage(string $quotaName): int
    {
        $usage = $this->usage()->where('quota_name', $quotaName)->first();
        return $usage ? $usage->current_usage : 0;
    }

    /**
     * Update usage quota.
     */
    public function updateUsage(string $quotaName, int $value): void
    {
        $this->usage()->updateOrCreate(
            ['quota_name' => $quotaName],
            [
                'current_usage' => $value,
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Increment usage quota.
     */
    public function incrementUsage(string $quotaName, int $amount = 1): void
    {
        $currentUsage = $this->getCurrentUsage($quotaName);
        $this->updateUsage($quotaName, $currentUsage + $amount);
    }

    /**
     * Check if quota limit is exceeded.
     */
    public function isQuotaExceeded(string $quotaName): bool
    {
        $limit = $this->package->getQuotaLimit($quotaName);
        if ($limit === null || $limit === -1) { // -1 means unlimited
            return false;
        }
        
        $current = $this->getCurrentUsage($quotaName);
        return $current >= $limit;
    }

    /**
     * Check if feature is available.
     */
    public function hasFeature(string $featureName): bool
    {
        return $this->package->hasFeature($featureName);
    }
}
