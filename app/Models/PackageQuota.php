<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageQuota extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'quota_name',
        'quota_description',
        'quota_limit',
        'quota_unit',
    ];

    protected $casts = [
        'package_id' => 'integer',
        'quota_limit' => 'integer',
    ];

    /**
     * Get the package that owns this quota.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Format quota limit with unit.
     */
    public function getFormattedLimitAttribute(): string
    {
        return $this->quota_limit . ' ' . $this->quota_unit;
    }

    /**
     * Check if quota is unlimited.
     */
    public function isUnlimited(): bool
    {
        return $this->quota_limit === -1;
    }
}
