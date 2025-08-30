<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupDatabase extends Command
{
    protected $signature = 'lms:setup-db';
    protected $description = 'Setup database tables manually (workaround for mbstring issue)';

    public function handle()
    {
        $this->info('Setting up LMS database tables...');

        try {
            // Create tenants table
            if (!Schema::hasTable('tenants')) {
                DB::statement("
                    CREATE TABLE tenants (
                        id VARCHAR(191) PRIMARY KEY,
                        name VARCHAR(191) NOT NULL,
                        slug VARCHAR(191) UNIQUE NOT NULL,
                        domain VARCHAR(191) UNIQUE NULL,
                        database VARCHAR(191) NULL,
                        data JSON NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                ");
                $this->info('✓ Created tenants table');
            }

            // Create domains table
            if (!Schema::hasTable('domains')) {
                DB::statement("
                    CREATE TABLE domains (
                        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        domain VARCHAR(191) UNIQUE NOT NULL,
                        tenant_id VARCHAR(191) NOT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
                    )
                ");
                $this->info('✓ Created domains table');
            }

            // Create roles table
            if (!Schema::hasTable('roles')) {
                DB::statement("
                    CREATE TABLE roles (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        tenant_id VARCHAR(191) NULL,
                        name VARCHAR(191) NOT NULL,
                        guard_name VARCHAR(191) NOT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        UNIQUE KEY unique_role (tenant_id, name, guard_name),
                        KEY tenant_id_index (tenant_id),
                        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
                    )
                ");
                $this->info('✓ Created roles table');
            }

            // Create permissions table
            if (!Schema::hasTable('permissions')) {
                DB::statement("
                    CREATE TABLE permissions (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        tenant_id VARCHAR(191) NULL,
                        name VARCHAR(191) NOT NULL,
                        guard_name VARCHAR(191) NOT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        UNIQUE KEY unique_permission (tenant_id, name, guard_name),
                        KEY tenant_id_index (tenant_id),
                        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
                    )
                ");
                $this->info('✓ Created permissions table');
            }

            // Create users table
            if (!Schema::hasTable('users')) {
                DB::statement("
                    CREATE TABLE users (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        tenant_id VARCHAR(191) NOT NULL,
                        name VARCHAR(191) NOT NULL,
                        email VARCHAR(191) NOT NULL,
                        email_verified_at TIMESTAMP NULL,
                        password VARCHAR(191) NOT NULL,
                        phone VARCHAR(191) NULL,
                        date_of_birth DATE NULL,
                        gender ENUM('male', 'female', 'other') NULL,
                        bio TEXT NULL,
                        avatar VARCHAR(191) NULL,
                        preferences JSON NULL,
                        is_active BOOLEAN DEFAULT 1,
                        last_login_at TIMESTAMP NULL,
                        remember_token VARCHAR(100) NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        UNIQUE KEY unique_user_email (tenant_id, email),
                        KEY tenant_id_index (tenant_id),
                        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
                    )
                ");
                $this->info('✓ Created users table');
            }

            // Create permission relation tables
            if (!Schema::hasTable('model_has_roles')) {
                DB::statement("
                    CREATE TABLE model_has_roles (
                        role_id BIGINT UNSIGNED NOT NULL,
                        model_type VARCHAR(191) NOT NULL,
                        model_id BIGINT UNSIGNED NOT NULL,
                        PRIMARY KEY (role_id, model_id, model_type),
                        KEY model_has_roles_model_id_model_type_index (model_id, model_type),
                        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
                    )
                ");
                $this->info('✓ Created model_has_roles table');
            }

            // Create courses table
            if (!Schema::hasTable('courses')) {
                DB::statement("
                    CREATE TABLE courses (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        tenant_id VARCHAR(191) NOT NULL,
                        strapi_course_id VARCHAR(191) NULL,
                        title VARCHAR(191) NOT NULL,
                        slug VARCHAR(191) NOT NULL,
                        description TEXT NULL,
                        short_description TEXT NULL,
                        thumbnail VARCHAR(191) NULL,
                        price DECIMAL(10,2) DEFAULT 0,
                        currency VARCHAR(3) DEFAULT 'USD',
                        status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
                        difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
                        duration_hours INT NULL,
                        tags JSON NULL,
                        learning_outcomes JSON NULL,
                        prerequisites JSON NULL,
                        is_featured BOOLEAN DEFAULT 0,
                        max_enrollments INT NULL,
                        enrollment_start_date DATE NULL,
                        enrollment_end_date DATE NULL,
                        course_start_date DATE NULL,
                        course_end_date DATE NULL,
                        metadata JSON NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        UNIQUE KEY unique_course_slug (tenant_id, slug),
                        KEY tenant_id_index (tenant_id),
                        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
                    )
                ");
                $this->info('✓ Created courses table');
            }

            $this->info('Database setup completed successfully!');
            $this->info('You can now run: php artisan db:seed');

        } catch (\Exception $e) {
            $this->error('Database setup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
