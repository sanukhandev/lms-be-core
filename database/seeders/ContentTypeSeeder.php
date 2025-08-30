<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding content types...');

        $contentTypes = [
            [
                'name' => 'video',
                'display_name' => 'Video',
                'description' => 'Video content including MP4, AVI, and other video formats',
                'is_active' => true,
            ],
            [
                'name' => 'text',
                'display_name' => 'Text Article',
                'description' => 'Text-based content including articles and written materials',
                'is_active' => true,
            ],
            [
                'name' => 'quiz',
                'display_name' => 'Quiz',
                'description' => 'Interactive quizzes and assessments',
                'is_active' => true,
            ],
            [
                'name' => 'assignment',
                'display_name' => 'Assignment',
                'description' => 'Student assignments and homework',
                'is_active' => true,
            ],
            [
                'name' => 'resource',
                'display_name' => 'Resource',
                'description' => 'Downloadable resources like PDFs, documents, and files',
                'is_active' => true,
            ],
            [
                'name' => 'live_session',
                'display_name' => 'Live Session',
                'description' => 'Live streaming sessions and webinars',
                'is_active' => true,
            ],
            [
                'name' => 'audio',
                'display_name' => 'Audio',
                'description' => 'Audio content including podcasts and music',
                'is_active' => true,
            ],
            [
                'name' => 'presentation',
                'display_name' => 'Presentation',
                'description' => 'Slide presentations and interactive content',
                'is_active' => true,
            ],
            [
                'name' => 'document',
                'display_name' => 'Document',
                'description' => 'Text documents and written materials',
                'is_active' => true,
            ],
            [
                'name' => 'interactive',
                'display_name' => 'Interactive Content',
                'description' => 'Interactive elements like simulations and games',
                'is_active' => true,
            ],
        ];

        foreach ($contentTypes as $contentType) {
            DB::table('content_types')->updateOrInsert(
                ['name' => $contentType['name']], // Match condition
                array_merge($contentType, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Content types seeded successfully');
    }
}
