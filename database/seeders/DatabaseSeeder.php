<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting comprehensive database seeding...');
        
        $this->call([
            // Core system setup
            TenantSeeder::class,
            PackageSeeder::class,
            IntegrationProviderSeeder::class,
            ContentTypeSeeder::class,
            AssignmentTypeSeeder::class,
            
            // User and permissions
            RolePermissionSeeder::class,
            UserSeeder::class,
            
            // Content structure
            CategorySeeder::class,
            TagSeeder::class,
            CourseSeeder::class,
            ModuleSeeder::class,
            ChapterSeeder::class,
            
            // Student activity
            EnrollmentSeeder::class,
            ProgressSeeder::class,
            AssignmentSeeder::class,
            SubmissionSeeder::class,
            
            // Commerce and certificates
            OrderSeeder::class,
            CertificateSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('🎯 Demo credentials:');
        $this->command->info('   Admin: admin@demo.lms / password123');
        $this->command->info('   Instructor: instructor@demo.lms / password123');
        $this->command->info('   Student: student@demo.lms / password123');
    }
}
