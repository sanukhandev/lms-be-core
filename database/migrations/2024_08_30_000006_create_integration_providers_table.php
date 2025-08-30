<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Integration providers table - normalized integration types
        Schema::create('integration_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // strapi, box, youtube, gemini, stripe
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('required_fields'); // fields needed for this integration
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['name', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_providers');
    }
};
