<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class BaseModel extends Model implements Auditable
{
    use AuditableTrait;

    /**
     * Boot the model and apply global scopes.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            // Auto-set tenant_id if tenant context exists and model has tenant_id column
            if (tenant() && $model->hasTenantColumn()) {
                $model->tenant_id = tenant('id');
            }
        });
    }

    /**
     * Check if the model has a tenant_id column.
     */
    protected function hasTenantColumn(): bool
    {
        return in_array('tenant_id', $this->fillable) || 
               (property_exists($this, 'attributes') && array_key_exists('tenant_id', $this->attributes));
    }

    /**
     * Scope to filter by current tenant.
     */
    public function scopeTenant($query)
    {
        if (tenant() && $this->hasTenantColumn()) {
            return $query->where('tenant_id', tenant('id'));
        }
        
        return $query;
    }
}