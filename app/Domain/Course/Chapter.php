<?php

namespace App\Domain\Course;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Chapter extends Model implements Auditable
{
    use HasFactory, BelongsToTenant, AuditableTrait;

    protected $fillable = [
        'tenant_id',
        'module_id',
        'strapi_chapter_id',
        'title',
        'content',
        'order',
        'duration_minutes',
        'content_type',
        'video_url',
        'video_id',
        'box_file_id',
        'resources',
        'is_mandatory',
        'is_preview',
        'completed_by_users',
        'metadata',
    ];

    protected $casts = [
        'order' => 'integer',
        'duration_minutes' => 'integer',
        'resources' => 'array',
        'is_mandatory' => 'boolean',
        'is_preview' => 'boolean',
        'completed_by_users' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the module that owns this chapter.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Check if chapter is completed by user.
     */
    public function isCompletedByUser(int $userId): bool
    {
        return in_array($userId, $this->completed_by_users ?? []);
    }

    /**
     * Mark chapter as completed by user.
     */
    public function markCompletedByUser(int $userId): void
    {
        $completedUsers = $this->completed_by_users ?? [];
        
        if (!in_array($userId, $completedUsers)) {
            $completedUsers[] = $userId;
            $this->update(['completed_by_users' => $completedUsers]);
        }
    }

    /**
     * Mark chapter as incomplete by user.
     */
    public function markIncompleteByUser(int $userId): void
    {
        $completedUsers = $this->completed_by_users ?? [];
        $completedUsers = array_diff($completedUsers, [$userId]);
        $this->update(['completed_by_users' => array_values($completedUsers)]);
    }

    /**
     * Check if chapter has video content.
     */
    public function hasVideo(): bool
    {
        return !empty($this->video_url) || !empty($this->video_id);
    }

    /**
     * Check if chapter has file resources.
     */
    public function hasFiles(): bool
    {
        return !empty($this->box_file_id) || !empty($this->resources);
    }

    /**
     * Get YouTube embed URL.
     */
    public function getYouTubeEmbedUrl(): ?string
    {
        if (empty($this->video_id)) {
            return null;
        }

        return "https://www.youtube.com/embed/{$this->video_id}";
    }
}
