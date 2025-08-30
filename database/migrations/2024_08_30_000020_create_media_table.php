<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Media table - normalized media storage for chapters
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('chapter_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('media_type', ['video', 'audio', 'document', 'image', 'archive']);
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size'); // in bytes
            $table->integer('duration_seconds')->nullable(); // for video/audio
            $table->string('external_id')->nullable(); // YouTube video ID, Box file ID
            $table->string('external_url')->nullable(); // External URL
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'chapter_id']);
            $table->index(['media_type']);
            $table->index(['external_id']);
            $table->index(['file_size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
