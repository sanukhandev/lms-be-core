<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFeature extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'feature_name',
        'feature_description',
        'feature_type',
        'feature_value',
    ];

    protected $casts = [
        'package_id' => 'integer',
    ];

    /**
     * Get the package that owns this feature.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the feature value as the correct type.
     */
    public function getTypedValueAttribute()
    {
        return match($this->feature_type) {
            'boolean' => filter_var($this->feature_value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->feature_value,
            'float' => (float) $this->feature_value,
            default => $this->feature_value,
        };
    }

    /**
     * Check if feature is enabled.
     */
    public function isEnabled(): bool
    {
        if ($this->feature_type === 'boolean') {
            return $this->getTypedValueAttribute();
        }
        return !empty($this->feature_value);
    }
}
