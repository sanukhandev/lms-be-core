<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding users...');

        // Create admin user
        $adminId = DB::table('users')->insertGetId([
            'tenant_id' => 'demo',
            'name' => 'Admin User',
            'email' => 'admin@demo.lms',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'phone' => '+1-555-0001',
            'bio' => 'System administrator for Demo University',
            'timezone' => 'UTC',
            'language' => 'en',
            'is_active' => true,
            'last_login_at' => now()->subHours(2),
            'notification_preferences' => json_encode([
                'email_notifications' => true,
                'sms_notifications' => false,
                'push_notifications' => true
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create instructor users
        $instructors = [
            [
                'name' => 'Dr. Sarah Johnson',
                'email' => 'instructor@demo.lms',
                'bio' => 'Professor of Computer Science with 15 years of teaching experience',
                'phone' => '+1-555-0002',
            ],
            [
                'name' => 'Prof. Michael Chen',
                'email' => 'michael.chen@demo.lms',
                'bio' => 'Data Science expert and machine learning researcher',
                'phone' => '+1-555-0003',
            ],
            [
                'name' => 'Dr. Emily Rodriguez',
                'email' => 'emily.rodriguez@demo.lms',
                'bio' => 'Web development specialist and UX design instructor',
                'phone' => '+1-555-0004',
            ],
            [
                'name' => 'Prof. David Kim',
                'email' => 'david.kim@demo.lms',
                'bio' => 'Mobile app development and software engineering professor',
                'phone' => '+1-555-0005',
            ],
        ];

        $instructorIds = [];
        foreach ($instructors as $instructor) {
            $instructorIds[] = DB::table('users')->insertGetId(array_merge($instructor, [
                'tenant_id' => 'demo',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'timezone' => 'UTC',
                'language' => 'en',
                'is_active' => true,
                'last_login_at' => now()->subHours(rand(1, 24)),
                'notification_preferences' => json_encode([
                    'email_notifications' => true,
                    'sms_notifications' => false,
                    'push_notifications' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create student users
        $students = [
            [
                'name' => 'John Doe',
                'email' => 'student@demo.lms',
                'bio' => 'Computer Science student passionate about web development',
                'phone' => '+1-555-1001',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@demo.lms',
                'bio' => 'Aspiring data scientist with background in mathematics',
                'phone' => '+1-555-1002',
            ],
            [
                'name' => 'Alex Thompson',
                'email' => 'alex.thompson@demo.lms',
                'bio' => 'Mobile app developer looking to expand skills',
                'phone' => '+1-555-1003',
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@demo.lms',
                'bio' => 'UX designer transitioning to frontend development',
                'phone' => '+1-555-1004',
            ],
            [
                'name' => 'Robert Wilson',
                'email' => 'robert.wilson@demo.lms',
                'bio' => 'Software engineer seeking to learn machine learning',
                'phone' => '+1-555-1005',
            ],
            [
                'name' => 'Lisa Brown',
                'email' => 'lisa.brown@demo.lms',
                'bio' => 'Marketing professional learning digital skills',
                'phone' => '+1-555-1006',
            ],
        ];

        $studentIds = [];
        foreach ($students as $student) {
            $studentIds[] = DB::table('users')->insertGetId(array_merge($student, [
                'tenant_id' => 'demo',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'timezone' => 'UTC',
                'language' => 'en',
                'is_active' => true,
                'last_login_at' => now()->subHours(rand(1, 72)),
                'notification_preferences' => json_encode([
                    'email_notifications' => true,
                    'sms_notifications' => false,
                    'push_notifications' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Assign roles
        $roles = [
            'tenant_admin' => [$adminId],
            'instructor' => $instructorIds,
            'student' => $studentIds,
        ];

        foreach ($roles as $roleName => $userIds) {
            foreach ($userIds as $userId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => Role::where('name', $roleName)->first()->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId,
                ]);
            }
        }

        // Store IDs for use in other seeders
        $this->command->info('Admin ID: ' . $adminId);
        $this->command->info('Instructor IDs: ' . implode(', ', $instructorIds));
        $this->command->info('Student IDs: ' . implode(', ', $studentIds));

        $this->command->info('✅ Users seeded successfully');
    }
}
