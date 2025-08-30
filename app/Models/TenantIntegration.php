<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantIntegration extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'integration_provider_id',
        'configuration',
        'is_enabled',
        'last_sync_at',
        'sync_status',
    ];

    protected $casts = [
        'configuration' => 'encrypted:array',
        'is_enabled' => 'boolean',
        'last_sync_at' => 'datetime',
        'sync_status' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the integration provider.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(IntegrationProvider::class, 'integration_provider_id');
    }

    /**
     * Get tenant settings.
     */
    public function tenantSettings(): BelongsTo
    {
        return $this->belongsTo(TenantSettings::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Scope for enabled integrations.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Get configuration value.
     */
    public function getConfigValue(string $key, $default = null)
    {
        $config = $this->configuration ?? [];
        return $config[$key] ?? $default;
    }

    /**
     * Set configuration value.
     */
    public function setConfigValue(string $key, $value): void
    {
        $config = $this->configuration ?? [];
        $config[$key] = $value;
        $this->update(['configuration' => $config]);
    }

    /**
     * Check if integration is properly configured.
     */
    public function isConfigured(): bool
    {
        if (!$this->is_enabled) {
            return false;
        }

        $requiredFields = $this->provider->required_fields ?? [];
        $config = $this->configuration ?? [];

        foreach ($requiredFields as $field) {
            if (empty($config[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update sync status.
     */
    public function updateSyncStatus(string $status, ?string $message = null): void
    {
        $this->update([
            'last_sync_at' => now(),
            'sync_status' => [
                'status' => $status,
                'message' => $message,
                'timestamp' => now()->toISOString(),
            ],
        ]);
    }
}
