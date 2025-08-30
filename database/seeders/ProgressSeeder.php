<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgressSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding chapter progress...');

        // Get enrollments and their chapters
        $enrollments = DB::table('enrollments')->where('tenant_id', 'demo')->get();

        foreach ($enrollments as $enrollment) {
            // Get chapters for this course
            $chapters = DB::table('chapters')
                ->join('modules', 'chapters.module_id', '=', 'modules.id')
                ->where('modules.course_id', $enrollment->course_id)
                ->where('chapters.tenant_id', 'demo')
                ->select('chapters.*')
                ->get();

            foreach ($chapters as $chapter) {
                // Randomly determine if this chapter was started/completed
                $started = rand(1, 100) <= $enrollment->progress_percentage;
                
                if ($started) {
                    $completed = rand(1, 100) <= 80; // 80% chance completed if started
                    $progress = $completed ? 100 : rand(25, 95);
                    
                    $startedAt = now()->subDays(rand(1, 20));
                    $completedAt = $completed ? $startedAt->copy()->addMinutes(rand(10, 60)) : null;

                    DB::table('chapter_progress')->insert([
                        'enrollment_id' => $enrollment->id,
                        'chapter_id' => $chapter->id,
                        'started_at' => $startedAt,
                        'completed_at' => $completedAt,
                        'time_spent_minutes' => rand(5, 45),
                        'progress_percentage' => $progress,
                        'created_at' => $startedAt,
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ Chapter progress seeded successfully');
    }
}
