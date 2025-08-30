<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend Laravel's default users table with tenant-specific fields
        Schema::table('users', function (Blueprint $table) {
            $table->string('tenant_id')->nullable()->index()->after('id');
            $table->string('phone')->nullable()->after('email_verified_at');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('date_of_birth');
            $table->text('bio')->nullable()->after('gender');
            $table->string('avatar_url')->nullable()->after('bio');
            $table->string('timezone', 50)->nullable()->after('avatar_url');
            $table->string('language', 5)->nullable()->after('timezone');
            $table->boolean('is_active')->default(true)->after('language');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->json('notification_preferences')->nullable()->after('last_login_at');
            
            $table->index(['tenant_id', 'email']);
            $table->index(['tenant_id', 'is_active']);
            $table->index(['last_login_at']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'email']);
            $table->dropIndex(['tenant_id', 'is_active']);
            $table->dropIndex(['last_login_at']);
            $table->dropIndex(['is_active']);
            
            $table->dropColumn([
                'tenant_id', 'phone', 'date_of_birth', 'gender', 
                'bio', 'avatar_url', 'timezone', 'language',
                'is_active', 'last_login_at', 'notification_preferences'
            ]);
        });
    }
};
