<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantBranding extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'logo_url',
        'favicon_url',
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_color',
        'text_color',
        'custom_css',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the tenant settings.
     */
    public function settings(): BelongsTo
    {
        return $this->belongsTo(TenantSettings::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get CSS variables for theming.
     */
    public function getCssVariablesAttribute(): array
    {
        return [
            '--primary-color' => $this->primary_color,
            '--secondary-color' => $this->secondary_color,
            '--accent-color' => $this->accent_color,
            '--background-color' => $this->background_color,
            '--text-color' => $this->text_color,
        ];
    }

    /**
     * Get brand colors as array.
     */
    public function getColorsAttribute(): array
    {
        return [
            'primary' => $this->primary_color,
            'secondary' => $this->secondary_color,
            'accent' => $this->accent_color,
            'background' => $this->background_color,
            'text' => $this->text_color,
        ];
    }
}
