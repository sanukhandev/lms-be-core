<?php

namespace App\Domain\Enrollment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Enrollment extends Model implements Auditable
{
    use HasFactory, BelongsToTenant, AuditableTrait;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'course_id',
        'status',
        'enrolled_at',
        'started_at',
        'completed_at',
        'expired_at',
        'progress_percentage',
        'last_accessed_at',
        'certificate_issued',
        'certificate_data',
        'metadata',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expired_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'certificate_issued' => 'boolean',
        'certificate_data' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns this enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\User::class);
    }

    /**
     * Get the course for this enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Course\Course::class);
    }

    /**
     * Check if enrollment is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->expired_at === null || $this->expired_at > now());
    }

    /**
     * Check if enrollment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->progress_percentage >= 100;
    }

    /**
     * Check if enrollment is expired.
     */
    public function isExpired(): bool
    {
        return $this->expired_at !== null && $this->expired_at <= now();
    }

    /**
     * Start the enrollment.
     */
    public function start(): void
    {
        $this->update([
            'started_at' => now(),
            'status' => 'active',
            'last_accessed_at' => now(),
        ]);
    }

    /**
     * Complete the enrollment.
     */
    public function complete(): void
    {
        $this->update([
            'completed_at' => now(),
            'status' => 'completed',
            'progress_percentage' => 100,
        ]);
    }

    /**
     * Update progress percentage.
     */
    public function updateProgress(float $percentage): void
    {
        $this->update([
            'progress_percentage' => min(100, max(0, $percentage)),
            'last_accessed_at' => now(),
        ]);

        // Auto-complete if progress reaches 100%
        if ($percentage >= 100 && !$this->isCompleted()) {
            $this->complete();
        }
    }

    /**
     * Update last accessed timestamp.
     */
    public function updateLastAccessed(): void
    {
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Issue certificate.
     */
    public function issueCertificate(array $certificateData = []): void
    {
        $this->update([
            'certificate_issued' => true,
            'certificate_data' => array_merge([
                'issued_at' => now()->toISOString(),
                'certificate_id' => uniqid('cert_'),
            ], $certificateData),
        ]);
    }

    /**
     * Calculate and update progress based on course completion.
     */
    public function calculateProgress(): float
    {
        $progress = $this->course->getProgressForUser($this->user_id);
        $this->updateProgress($progress);
        
        return $progress;
    }
}
