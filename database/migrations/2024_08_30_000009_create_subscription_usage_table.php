<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subscription usage table - tracks current usage against quotas
        Schema::create('subscription_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('quota_name');
            $table->bigInteger('current_usage')->default(0);
            $table->timestamp('last_updated_at');
            $table->timestamps();

            $table->unique(['subscription_id', 'quota_name']);
            $table->index(['quota_name', 'current_usage']);
            $table->index(['last_updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usage');
    }
};
