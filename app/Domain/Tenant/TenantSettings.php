<?php

namespace App\Domain\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class TenantSettings extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'tenant_id',
        'branding',
        'features',
        'quotas',
        'white_label_settings',
        'notification_settings',
        'security_settings',
    ];

    protected $casts = [
        'branding' => 'array',
        'features' => 'array',
        'quotas' => 'array',
        'white_label_settings' => 'array',
        'notification_settings' => 'array',
        'security_settings' => 'array',
    ];

    /**
     * Get the tenant that owns this settings.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get a specific branding setting.
     */
    public function getBrandingSetting(string $key, $default = null)
    {
        return data_get($this->branding, $key, $default);
    }

    /**
     * Check if a feature is enabled.
     */
    public function isFeatureEnabled(string $feature): bool
    {
        return data_get($this->features, $feature, false);
    }

    /**
     * Get quota for a specific resource.
     */
    public function getQuota(string $resource): ?int
    {
        return data_get($this->quotas, $resource);
    }

    /**
     * Check if quota is exceeded for a resource.
     */
    public function isQuotaExceeded(string $resource, int $currentUsage): bool
    {
        $quota = $this->getQuota($resource);
        return $quota !== null && $currentUsage >= $quota;
    }
}
