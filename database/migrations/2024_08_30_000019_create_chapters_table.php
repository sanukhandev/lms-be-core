<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chapters table - module chapters/lessons
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_type_id')->constrained()->onDelete('restrict');
            $table->string('strapi_chapter_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->longText('content')->nullable(); // HTML content for text type
            $table->integer('estimated_duration_minutes')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_free_preview')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['module_id', 'slug']);
            $table->index(['tenant_id', 'module_id', 'sort_order']);
            $table->index(['module_id', 'content_type_id', 'is_published']);
            $table->index(['is_free_preview']);
            $table->index(['strapi_chapter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
