<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Package quotas table - normalized quotas for packages
        Schema::create('package_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('quota_name');
            $table->string('quota_description')->nullable();
            $table->bigInteger('quota_limit');
            $table->string('quota_unit')->default('count'); // count, mb, gb, hours, etc.
            $table->timestamps();

            $table->unique(['package_id', 'quota_name']);
            $table->index(['quota_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_quotas');
    }
};
