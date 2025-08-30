<?php

namespace App\Domain\Course;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Module extends Model implements Auditable
{
    use HasFactory, BelongsToTenant, AuditableTrait;

    protected $fillable = [
        'tenant_id',
        'course_id',
        'strapi_module_id',
        'title',
        'description',
        'order',
        'duration_minutes',
        'is_mandatory',
        'metadata',
    ];

    protected $casts = [
        'order' => 'integer',
        'duration_minutes' => 'integer',
        'is_mandatory' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the course that owns this module.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the module chapters.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    /**
     * Get the total duration in minutes.
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->chapters->sum('duration_minutes') ?: $this->duration_minutes;
    }

    /**
     * Check if module is completed by user.
     */
    public function isCompletedByUser(int $userId): bool
    {
        $totalChapters = $this->chapters->count();
        $completedChapters = $this->chapters->where('completed_by_users', 'like', "%{$userId}%")->count();

        return $totalChapters > 0 && $completedChapters === $totalChapters;
    }
}
