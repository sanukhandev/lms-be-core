<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Assignments table - course assignments
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('assignment_type_id')->constrained()->onDelete('restrict');
            $table->string('title');
            $table->text('description');
            $table->longText('instructions')->nullable();
            $table->decimal('max_points', 8, 2)->default(100.00);
            $table->integer('time_limit_minutes')->nullable();
            $table->integer('max_attempts')->default(1);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('due_at');
            $table->timestamp('late_submission_until')->nullable();
            $table->decimal('late_penalty_percentage', 5, 2)->default(0.00);
            $table->integer('max_file_size_mb')->default(10);
            $table->boolean('plagiarism_check_enabled')->default(false);
            $table->boolean('peer_review_enabled')->default(false);
            $table->integer('peer_reviews_required')->default(0);
            $table->enum('status', ['draft', 'published', 'closed', 'archived'])->default('draft');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'course_id', 'status']);
            $table->index(['tenant_id', 'instructor_id']);
            $table->index(['due_at', 'status']);
            $table->index(['available_from', 'due_at']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
