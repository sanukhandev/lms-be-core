<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantSettings extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'organization_name',
        'domain',
        'contact_email',
        'contact_phone',
        'timezone',
        'language',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the tenant branding.
     */
    public function branding(): HasOne
    {
        return $this->hasOne(TenantBranding::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the tenant integrations.
     */
    public function integrations()
    {
        return $this->hasMany(TenantIntegration::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get active subscription for this tenant.
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'tenant_id', 'tenant_id')
                    ->where('status', 'active');
    }

    /**
     * Get tenant's timezone.
     */
    public function getTimezoneAttribute($value): string
    {
        return $value ?? 'UTC';
    }

    /**
     * Scope for active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
