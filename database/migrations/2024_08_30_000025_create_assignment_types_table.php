<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Assignment types table - normalized assignment types
        Schema::create('assignment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // essay, project, quiz, presentation, code, file_upload
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('allows_file_upload')->default(true);
            $table->boolean('allows_text_submission')->default(true);
            $table->boolean('supports_auto_grading')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['name', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_types');
    }
};
