<?php

namespace App\Domain\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class TenantIntegrations extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'tenant_id',
        'strapi_config',
        'box_config',
        'youtube_config',
        'gemini_config',
        'stripe_config',
    ];

    protected $casts = [
        'strapi_config' => 'array',
        'box_config' => 'array',
        'youtube_config' => 'array',
        'gemini_config' => 'array',
        'stripe_config' => 'array',
    ];

    /**
     * Get the tenant that owns these integrations.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get Strapi configuration.
     */
    public function getStrapiConfig(): ?array
    {
        return $this->strapi_config;
    }

    /**
     * Get Box configuration.
     */
    public function getBoxConfig(): ?array
    {
        return $this->box_config;
    }

    /**
     * Get YouTube configuration.
     */
    public function getYouTubeConfig(): ?array
    {
        return $this->youtube_config;
    }

    /**
     * Get Gemini AI configuration.
     */
    public function getGeminiConfig(): ?array
    {
        return $this->gemini_config;
    }

    /**
     * Get Stripe configuration.
     */
    public function getStripeConfig(): ?array
    {
        return $this->stripe_config;
    }

    /**
     * Check if an integration is configured.
     */
    public function isIntegrationConfigured(string $integration): bool
    {
        $config = $this->{$integration . '_config'};
        return !empty($config) && !empty(array_filter($config));
    }
}
