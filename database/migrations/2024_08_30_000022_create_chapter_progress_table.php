<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chapter progress table - tracks individual chapter completion
        Schema::create('chapter_progress', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->integer('time_spent_minutes')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->json('progress_data')->nullable(); // video position, quiz attempts, etc.
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['enrollment_id', 'chapter_id']);
            $table->index(['tenant_id', 'enrollment_id']);
            $table->index(['chapter_id', 'is_completed']);
            $table->index(['last_accessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_progress');
    }
};
