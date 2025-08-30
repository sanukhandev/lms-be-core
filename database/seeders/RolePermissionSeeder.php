<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding roles and permissions...');

        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'users.view', 'users.create', 'users.update', 'users.delete',
            
            // Course management
            'courses.view', 'courses.create', 'courses.update', 'courses.delete',
            'courses.publish', 'courses.enroll', 'courses.moderate',
            
            // Content management
            'content.view', 'content.create', 'content.update', 'content.delete',
            'modules.manage', 'chapters.manage', 'media.manage',
            
            // Assignment management
            'assignments.view', 'assignments.create', 'assignments.update', 'assignments.delete',
            'assignments.grade', 'submissions.view', 'submissions.manage',
            
            // Student progress
            'progress.view', 'progress.update', 'certificates.issue',
            'enrollments.manage', 'grades.view', 'grades.manage',
            
            // Financial management
            'orders.view', 'orders.manage', 'payments.process', 'refunds.process',
            'subscriptions.manage', 'packages.manage',
            
            // Analytics and reports
            'analytics.view', 'reports.generate', 'exports.create',
            
            // System administration
            'system.settings', 'integrations.manage', 'branding.manage',
            'tenants.manage', 'roles.manage', 'permissions.manage',
            
            // Live sessions
            'sessions.create', 'sessions.manage', 'sessions.moderate',
            'attendance.track', 'webinars.host',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Create roles
        $superAdmin = Role::create(['name' => 'super_admin', 'guard_name' => 'api']);
        $tenantAdmin = Role::create(['name' => 'tenant_admin', 'guard_name' => 'api']);
        $instructor = Role::create(['name' => 'instructor', 'guard_name' => 'api']);
        $student = Role::create(['name' => 'student', 'guard_name' => 'api']);

        // Assign permissions to roles
        
        // Super Admin - all permissions
        $superAdmin->givePermissionTo(Permission::all());

        // Tenant Admin - all except system administration
        $tenantAdminPermissions = collect($permissions)->filter(function ($permission) {
            return !str_starts_with($permission, 'system.') && 
                   !str_starts_with($permission, 'tenants.');
        });
        $tenantAdmin->givePermissionTo($tenantAdminPermissions);

        // Instructor permissions
        $instructorPermissions = [
            'courses.view', 'courses.create', 'courses.update',
            'content.view', 'content.create', 'content.update',
            'modules.manage', 'chapters.manage', 'media.manage',
            'assignments.view', 'assignments.create', 'assignments.update', 'assignments.grade',
            'submissions.view', 'submissions.manage',
            'progress.view', 'certificates.issue',
            'enrollments.manage', 'grades.view', 'grades.manage',
            'sessions.create', 'sessions.manage', 'sessions.moderate',
            'attendance.track', 'webinars.host',
            'analytics.view', 'reports.generate',
        ];
        $instructor->givePermissionTo($instructorPermissions);

        // Student permissions
        $studentPermissions = [
            'courses.view', 'courses.enroll',
            'content.view',
            'assignments.view',
            'progress.view',
            'sessions.create', // for joining sessions
        ];
        $student->givePermissionTo($studentPermissions);

        $this->command->info('✅ Roles and permissions seeded successfully');
    }
}
