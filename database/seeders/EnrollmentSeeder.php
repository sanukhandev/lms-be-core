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
                $startedAt = $progress > 0 ? $enrolledAt->copy()->addHours(rand(1, 24)) : null;
                $expiresAt = $enrolledAt->copy()->addMonths(6); // 6 months access
                
                // Calculate chapter progress
                $totalChapters = DB::table('chapters')
                    ->join('modules', 'chapters.module_id', '=', 'modules.id')
                    ->where('modules.course_id', $course->id)
                    ->count();
                $completedChapters = (int) ($totalChapters * $progress / 100);

                DB::table('enrollments')->updateOrInsert(
                    [
                        'tenant_id' => 'demo',
                        'user_id' => $studentId,
                        'course_id' => $course->id
                    ], // Match condition
                    [
                        'status' => $completedAt ? 'completed' : 'active',
                        'enrolled_at' => $enrolledAt,
                        'started_at' => $startedAt,
                        'completed_at' => $completedAt,
                        'expires_at' => $expiresAt,
                        'progress_percentage' => $progress,
                        'completed_chapters' => $completedChapters,
                        'total_chapters' => $totalChapters,
                        'time_spent_minutes' => rand(30, 300), // 30 minutes to 5 hours
                        'final_grade' => $completedAt ? rand(70, 100) : null,
                        'created_at' => $enrolledAt,
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('✅ Enrollments seeded successfully');
    }
}
