<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modules table - course modules/sections
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('strapi_module_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['course_id', 'slug']);
            $table->index(['tenant_id', 'course_id', 'sort_order']);
            $table->index(['course_id', 'is_published']);
            $table->index(['strapi_module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
