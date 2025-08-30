<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'color',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Get courses with this tag.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_tags');
    }

    /**
     * Get course count for this tag.
     */
    public function getCourseCountAttribute(): int
    {
        return $this->courses()->count();
    }

    /**
     * Scope for tags with courses.
     */
    public function scopeWithCourses($query)
    {
        return $query->has('courses');
    }

    /**
     * Scope for popular tags (with most courses).
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('courses')
                    ->orderBy('courses_count', 'desc')
                    ->limit($limit);
    }
}
