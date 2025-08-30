<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Certificates table
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('certificate_hash')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('issued_at');
            $table->timestamp('expires_at')->nullable();
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->string('certificate_url')->nullable();
            $table->string('verification_url')->unique();
            $table->boolean('is_public')->default(true);
            $table->boolean('is_revoked')->default(false);
            $table->timestamp('revoked_at')->nullable();
            $table->text('revocation_reason')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'course_id']);
            $table->index(['certificate_number']);
            $table->index(['certificate_hash']);
            $table->index(['issued_at', 'expires_at']);
            $table->index(['is_revoked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
