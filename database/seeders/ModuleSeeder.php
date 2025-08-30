<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding modules...');

        // Get first few courses
        $courses = DB::table('courses')->where('tenant_id', 'demo')->limit(3)->get();

        foreach ($courses as $course) {
            // Create 3-5 modules per course
            $moduleCount = rand(3, 5);
            for ($i = 1; $i <= $moduleCount; $i++) {
                DB::table('modules')->insert([
                    'tenant_id' => 'demo',
                    'course_id' => $course->id,
                    'title' => "Module {$i}: " . $this->getModuleTitle($i),
                    'description' => "This module covers important concepts and practical applications.",
                    'sort_order' => $i,
                    'is_published' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Modules seeded successfully');
    }

    private function getModuleTitle($moduleNumber): string
    {
        $titles = [
            1 => 'Getting Started and Fundamentals',
            2 => 'Core Concepts and Principles',
            3 => 'Practical Applications',
            4 => 'Advanced Techniques',
            5 => 'Project Implementation',
        ];

        return $titles[$moduleNumber] ?? "Advanced Topic {$moduleNumber}";
    }
}
