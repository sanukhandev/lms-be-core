<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding assignment types...');

        $assignmentTypes = [
            [
                'name' => 'quiz',
                'display_name' => 'Quiz',
                'description' => 'Multiple choice and short answer questions',
                'allows_file_upload' => false,
                'allows_text_submission' => true,
                'supports_auto_grading' => true,
                'is_active' => true,
            ],
            [
                'name' => 'essay',
                'display_name' => 'Essay',
                'description' => 'Long-form written assignments',
                'allows_file_upload' => true,
                'allows_text_submission' => true,
                'supports_auto_grading' => false,
                'is_active' => true,
            ],
            [
                'name' => 'project',
                'display_name' => 'Project',
                'description' => 'Practical project submissions',
                'allows_file_upload' => true,
                'allows_text_submission' => true,
                'supports_auto_grading' => false,
                'is_active' => true,
            ],
            [
                'name' => 'presentation',
                'display_name' => 'Presentation',
                'description' => 'Oral presentations and demonstrations',
                'allows_file_upload' => true,
                'allows_text_submission' => false,
                'supports_auto_grading' => false,
                'is_active' => true,
            ],
            [
                'name' => 'code',
                'display_name' => 'Code Assignment',
                'description' => 'Programming assignments and coding challenges',
                'allows_file_upload' => true,
                'allows_text_submission' => true,
                'supports_auto_grading' => true,
                'is_active' => true,
            ],
            [
                'name' => 'file_upload',
                'display_name' => 'File Upload',
                'description' => 'General file submission assignments',
                'allows_file_upload' => true,
                'allows_text_submission' => false,
                'supports_auto_grading' => false,
                'is_active' => true,
            ],
        ];

        foreach ($assignmentTypes as $assignmentType) {
            DB::table('assignment_types')->updateOrInsert(
                ['name' => $assignmentType['name']], // Match condition
                array_merge($assignmentType, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Assignment types seeded successfully');
    }
}
