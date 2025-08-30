<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tenant branding table - separated branding from settings for 3NF
        Schema::create('tenant_branding', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->unique();
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('primary_color', 7)->default('#007bff');
            $table->string('secondary_color', 7)->default('#6c757d');
            $table->string('accent_color', 7)->default('#28a745');
            $table->string('background_color', 7)->default('#ffffff');
            $table->string('text_color', 7)->default('#212529');
            $table->text('custom_css')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_branding');
    }
};
