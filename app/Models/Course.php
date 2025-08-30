<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'strapi_course_id',
        'category_id',
        'instructor_id',
        'title',
        'slug',
        'short_description',
        'description',
        'thumbnail_url',
        'level',
        'status',
        'price',
        'estimated_duration_hours',
        'language',
        'is_featured',
        'is_free',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'estimated_duration_hours' => 'integer',
        'is_featured' => 'boolean',
        'is_free' => 'boolean',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the category that owns the course.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the instructor that owns the course.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the modules for this course.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('sort_order');
    }

    /**
     * Get the enrollments for this course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the class sessions for this course.
     */
    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * Get the learning objectives for this course.
     */
    public function learningObjectives(): HasMany
    {
        return $this->hasMany(CourseLearningObjective::class)->orderBy('sort_order');
    }

    /**
     * Get the prerequisites for this course.
     */
    public function prerequisites(): HasMany
    {
        return $this->hasMany(CoursePrerequisite::class)->orderBy('sort_order');
    }

    /**
     * Get the tags for this course.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'course_tags');
    }

    /**
     * Scope for published courses.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at');
    }

    /**
     * Scope for featured courses.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for free courses.
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true)->where('price', 0);
    }

    /**
     * Scope for paid courses.
     */
    public function scopePaid($query)
    {
        return $query->where('is_free', false)->where('price', '>', 0);
    }

    /**
     * Scope for courses by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for courses by level.
     */
    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for courses by instructor.
     */
    public function scopeByInstructor($query, int $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Check if course is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at !== null;
    }

    /**
     * Check if course is free.
     */
    public function isFree(): bool
    {
        return $this->is_free || $this->price == 0;
    }

    /**
     * Get total chapters count.
     */
    public function getTotalChaptersAttribute(): int
    {
        return $this->modules->sum(function ($module) {
            return $module->chapters->count();
        });
    }

    /**
     * Get total enrolled students count.
     */
    public function getEnrolledStudentsCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'active')->count();
    }

    /**
     * Get total completed students count.
     */
    public function getCompletedStudentsCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'completed')->count();
    }

    /**
     * Get course completion rate.
     */
    public function getCompletionRateAttribute(): float
    {
        $total = $this->enrollments()->count();
        if ($total === 0) return 0;
        
        $completed = $this->getCompletedStudentsCountAttribute();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Check if user is enrolled in this course.
     */
    public function isEnrolledBy(User $user): bool
    {
        return $this->enrollments()
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->exists();
    }

    /**
     * Get course progress for a specific user.
     */
    public function getProgressForUser(User $user): ?Enrollment
    {
        return $this->enrollments()
                    ->where('user_id', $user->id)
                    ->first();
    }

    /**
     * Get estimated total duration in minutes.
     */
    public function getTotalDurationMinutesAttribute(): int
    {
        return $this->modules->sum('estimated_duration_minutes') ?? 0;
    }
}
