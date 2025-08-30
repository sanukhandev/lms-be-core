<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enrollments table - student course enrollments
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'completed', 'dropped', 'expired', 'suspended'])->default('active');
            $table->timestamp('enrolled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0.00);
            $table->integer('completed_chapters')->default(0);
            $table->integer('total_chapters')->default(0);
            $table->integer('time_spent_minutes')->default(0);
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'user_id', 'course_id']);
            $table->index(['tenant_id', 'user_id', 'status']);
            $table->index(['tenant_id', 'course_id', 'status']);
            $table->index(['status', 'expires_at']);
            $table->index(['enrolled_at']);
            $table->index(['progress_percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
