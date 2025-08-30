<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntegrationProvider extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'required_fields',
        'is_active',
    ];

    protected $casts = [
        'required_fields' => 'array',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get tenant integrations for this provider.
     */
    public function tenantIntegrations()
    {
        return $this->hasMany(TenantIntegration::class);
    }

    /**
     * Scope for active providers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if provider is configured for tenant.
     */
    public function isConfiguredForTenant(string $tenantId): bool
    {
        return $this->tenantIntegrations()
                    ->where('tenant_id', $tenantId)
                    ->where('is_enabled', true)
                    ->exists();
    }
}
