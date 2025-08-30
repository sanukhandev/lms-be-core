<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tenant integrations table - per tenant integration configurations
        Schema::create('tenant_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('integration_provider_id')->constrained()->onDelete('cascade');
            $table->json('configuration'); // encrypted configuration data
            $table->boolean('is_enabled')->default(false);
            $table->timestamp('last_sync_at')->nullable();
            $table->json('sync_status')->nullable(); // last sync status and errors
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'integration_provider_id']);
            $table->index(['tenant_id', 'is_enabled']);
            $table->index(['last_sync_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_integrations');
    }
};
