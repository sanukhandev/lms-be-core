<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Package features table - normalized features for packages
        Schema::create('package_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('feature_name');
            $table->text('feature_description')->nullable();
            $table->string('feature_type')->default('boolean'); // boolean, integer, string
            $table->string('feature_value')->nullable(); // stores the value as string
            $table->timestamps();

            $table->unique(['package_id', 'feature_name']);
            $table->index(['feature_name', 'feature_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_features');
    }
};
