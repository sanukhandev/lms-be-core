<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding assignment submissions...');

        $assignments = DB::table('assignments')->where('tenant_id', 'demo')->get();
        $students = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'student')
            ->where('users.tenant_id', 'demo')
            ->limit(3)
            ->pluck('users.id')
            ->toArray();

        foreach ($assignments as $assignment) {
            foreach ($students as $studentId) {
                // Check if student is enrolled in this course
                $enrollment = DB::table('enrollments')
                    ->where('course_id', $assignment->course_id)
                    ->where('student_id', $studentId)
                    ->first();

                if ($enrollment) {
                    $submittedAt = now()->subDays(rand(1, 10));
                    $isGraded = rand(1, 100) <= 70; // 70% chance it's graded
                    $pointsEarned = $isGraded ? round($assignment->points_possible * (rand(70, 100) / 100), 2) : null;

                    DB::table('assignment_submissions')->insert([
                        'tenant_id' => 'demo',
                        'assignment_id' => $assignment->id,
                        'student_id' => $studentId,
                        'attempt_number' => 1,
                        'status' => $isGraded ? 'graded' : 'submitted',
                        'content' => 'This is my submission for the assignment. I have completed all requirements.',
                        'submitted_at' => $submittedAt,
                        'graded_at' => $isGraded ? $submittedAt->copy()->addDays(rand(1, 3)) : null,
                        'points_earned' => $pointsEarned,
                        'points_possible' => $assignment->points_possible,
                        'instructor_feedback' => $isGraded ? 'Good work! Consider improving...' : null,
                        'is_late' => false,
                        'late_penalty_applied' => 0.00,
                        'time_spent_minutes' => rand(30, 120),
                        'created_at' => $submittedAt,
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ Assignment submissions seeded successfully');
    }
}
