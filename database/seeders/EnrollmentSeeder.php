<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding enrollments...');

        // Get students and courses
        $students = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'student')
            ->where('users.tenant_id', 'demo')
            ->pluck('users.id')
            ->toArray();

        $courses = DB::table('courses')->where('tenant_id', 'demo')->get();

        foreach ($students as $studentId) {
            // Enroll each student in 2-4 random courses
            $enrollmentCount = rand(2, 4);
            $selectedCourses = $courses->random($enrollmentCount);

            foreach ($selectedCourses as $course) {
                $enrolledAt = now()->subDays(rand(1, 30));
                $progress = rand(0, 100);
                $completedAt = $progress === 100 ? $enrolledAt->copy()->addDays(rand(1, 20)) : null;

                DB::table('enrollments')->insert([
                    'tenant_id' => 'demo',
                    'course_id' => $course->id,
                    'student_id' => $studentId,
                    'enrolled_at' => $enrolledAt,
                    'completed_at' => $completedAt,
                    'progress_percentage' => $progress,
                    'last_accessed_at' => now()->subHours(rand(1, 48)),
                    'status' => $completedAt ? 'completed' : 'active',
                    'created_at' => $enrolledAt,
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Enrollments seeded successfully');
    }
}
