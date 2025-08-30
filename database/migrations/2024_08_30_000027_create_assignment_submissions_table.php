<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Assignment submissions table
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['draft', 'submitted', 'graded', 'returned', 'late'])->default('draft');
            $table->longText('content')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->decimal('points_earned', 8, 2)->nullable();
            $table->decimal('points_possible', 8, 2)->nullable();
            $table->text('instructor_feedback')->nullable();
            $table->boolean('is_late')->default(false);
            $table->decimal('late_penalty_applied', 5, 2)->default(0.00);
            $table->integer('time_spent_minutes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'assignment_id', 'student_id', 'attempt_number'], 'assign_sub_tenant_assign_student_attempt_unique');
            $table->index(['tenant_id', 'student_id']);
            $table->index(['assignment_id', 'status']);
            $table->index(['submitted_at', 'graded_at']);
            $table->index(['is_late', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
