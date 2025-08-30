<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding assignments...');

        $courses = DB::table('courses')->where('tenant_id', 'demo')->limit(3)->get();
        $assignmentTypes = DB::table('assignment_types')->where('tenant_id', 'demo')->get();

        foreach ($courses as $course) {
            // Create 2-3 assignments per course
            for ($i = 1; $i <= 2; $i++) {
                $assignmentType = $assignmentTypes->random();
                
                DB::table('assignments')->insert([
                    'tenant_id' => 'demo',
                    'course_id' => $course->id,
                    'assignment_type_id' => $assignmentType->id,
                    'title' => "Assignment {$i}: " . $assignmentType->name,
                    'description' => "Complete this {$assignmentType->name} to demonstrate your understanding.",
                    'instructions' => "Follow the guidelines and submit your work by the due date.",
                    'due_date' => now()->addDays(rand(7, 30)),
                    'points_possible' => $assignmentType->default_points,
                    'attempts_allowed' => $assignmentType->name === 'Quiz' ? 3 : 1,
                    'time_limit_minutes' => $assignmentType->name === 'Quiz' ? 30 : null,
                    'is_published' => true,
                    'allow_late_submission' => true,
                    'late_penalty_percent' => 10.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Assignments seeded successfully');
    }
}
