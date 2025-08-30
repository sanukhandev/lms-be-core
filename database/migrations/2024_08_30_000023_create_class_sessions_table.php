<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Class sessions table - live/virtual classes
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('restrict');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['live', 'webinar', 'workshop', 'office_hours', 'recorded'])->default('live');
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('meeting_platform')->nullable(); // zoom, teams, google_meet, etc.
            $table->string('meeting_url')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->integer('max_participants')->nullable();
            $table->boolean('recording_enabled')->default(false);
            $table->string('recording_url')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'course_id', 'starts_at']);
            $table->index(['tenant_id', 'instructor_id', 'starts_at']);
            $table->index(['status', 'starts_at']);
            $table->index(['starts_at', 'ends_at']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
