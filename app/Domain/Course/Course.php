<?php

namespace App\Domain\Course;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Course extends Model implements Auditable
{
    use HasFactory, SoftDeletes, BelongsToTenant, AuditableTrait;

    protected $fillable = [
        'tenant_id',
        'strapi_course_id',
        'title',
        'slug',
        'description',
        'short_description',
        'featured_image',
        'category',
        'level',
        'language',
        'price',
        'duration_hours',
        'max_students',
        'status',
        'published_at',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_hours' => 'decimal:2',
        'max_students' => 'integer',
        'published_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns this course.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Tenant\Tenant::class);
    }

    /**
     * Get the course modules.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    /**
     * Get the course enrollments.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(\App\Domain\Enrollment\Enrollment::class);
    }

    /**
     * Get the course assignments.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(\App\Domain\Assignment\Assignment::class);
    }

    /**
     * Get the course class sessions.
     */
    public function classSessions(): HasMany
    {
        return $this->hasMany(\App\Domain\ClassSession\ClassSession::class);
    }

    /**
     * Check if course is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    /**
     * Check if course is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Get enrolled students count.
     */
    public function getEnrolledStudentsCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'active')->count();
    }

    /**
     * Check if course has available spots.
     */
    public function hasAvailableSpots(): bool
    {
        if (!$this->max_students) {
            return true;
        }

        return $this->enrolled_students_count < $this->max_students;
    }

    /**
     * Get course progress for a specific user.
     */
    public function getProgressForUser(int $userId): float
    {
        $totalChapters = $this->modules->sum(fn($module) => $module->chapters->count());
        
        if ($totalChapters === 0) {
            return 0;
        }

        $completedChapters = $this->modules->sum(function ($module) use ($userId) {
            return $module->chapters->where('completed_by_users', 'like', "%{$userId}%")->count();
        });

        return round(($completedChapters / $totalChapters) * 100, 2);
    }

    /**
     * Sync course data from Strapi.
     */
    public function syncFromStrapi(array $strapiData): void
    {
        $this->update([
            'title' => $strapiData['title'] ?? $this->title,
            'slug' => $strapiData['slug'] ?? $this->slug,
            'description' => $strapiData['description'] ?? $this->description,
            'short_description' => $strapiData['shortDescription'] ?? $this->short_description,
            'featured_image' => $strapiData['featuredImage'] ?? $this->featured_image,
            'category' => $strapiData['category'] ?? $this->category,
            'level' => $strapiData['level'] ?? $this->level,
            'language' => $strapiData['language'] ?? $this->language,
            'duration_hours' => $strapiData['durationHours'] ?? $this->duration_hours,
            'status' => $strapiData['status'] ?? $this->status,
            'metadata' => array_merge($this->metadata ?? [], $strapiData['metadata'] ?? []),
        ]);
    }
}
