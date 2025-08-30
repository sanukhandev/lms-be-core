<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Courses table - main course entity
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('strapi_course_id')->nullable(); // Reference to Strapi CMS
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('restrict');
            $table->string('title');
            $table->string('slug');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('estimated_duration_hours')->nullable();
            $table->string('language', 5)->default('en');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_free')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status', 'published_at']);
            $table->index(['tenant_id', 'category_id', 'status']);
            $table->index(['tenant_id', 'instructor_id']);
            $table->index(['level', 'is_featured']);
            $table->index(['is_free', 'price']);
            $table->index(['strapi_course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
