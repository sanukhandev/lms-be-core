<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding chapters...');

        // Get modules and content types
        $modules = DB::table('modules')->where('tenant_id', 'demo')->get();
        $contentTypes = DB::table('content_types')->get()->keyBy('name');

        foreach ($modules as $module) {
            // Create 2-4 chapters per module
            $chapterCount = rand(2, 4);
            for ($i = 1; $i <= $chapterCount; $i++) {
                $contentType = $this->getRandomContentType($contentTypes);
                $title = "Lesson {$i}: " . $this->getChapterTitle($i);
                $slug = Str::slug($title);
                
                DB::table('chapters')->updateOrInsert(
                    [
                        'module_id' => $module->id,
                        'slug' => $slug
                    ], // Match condition
                    [
                        'tenant_id' => 'demo',
                        'content_type_id' => $contentType->id,
                        'strapi_chapter_id' => "chapter_{$module->id}_{$i}",
                        'title' => $title,
                        'description' => $this->getChapterDescription($contentType->name),
                        'content' => $this->getChapterContent($contentType->name),
                        'estimated_duration_minutes' => rand(10, 30),
                        'sort_order' => $i,
                        'is_published' => true,
                        'is_free_preview' => $i === 1, // First lesson is free preview
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('✅ Chapters seeded successfully');
    }

    private function getRandomContentType($contentTypes)
    {
        $types = ['video', 'text', 'quiz', 'resource'];
        $randomType = $types[array_rand($types)];
        return $contentTypes[$randomType];
    }

    private function getChapterDescription($contentType): string
    {
        switch ($contentType) {
            case 'video':
                return 'Watch this comprehensive video lesson with practical demonstrations.';
            case 'text':
                return 'Read through detailed explanations and examples in this text-based lesson.';
            case 'quiz':
                return 'Test your understanding with this interactive quiz.';
            case 'resource':
                return 'Download and review additional resources and materials.';
            default:
                return 'Explore this lesson to deepen your understanding of the topic.';
        }
    }

    private function getChapterTitle($chapterNumber): string
    {
        $titles = [
            1 => 'Introduction and Overview',
            2 => 'Key Concepts Explained',
            3 => 'Hands-on Practice',
            4 => 'Real-world Examples',
        ];

        return $titles[$chapterNumber] ?? "Advanced Topic {$chapterNumber}";
    }

    private function getChapterContent($contentType): string
    {
        switch ($contentType) {
            case 'video':
                return 'This video lesson covers the fundamental concepts with visual demonstrations and examples.';
            case 'text':
                return '<h2>Learning Objectives</h2><p>In this lesson, you will learn...</p><h3>Key Points</h3><ul><li>Important concept 1</li><li>Important concept 2</li><li>Important concept 3</li></ul>';
            case 'quiz':
                return json_encode([
                    'questions' => [
                        [
                            'question' => 'What is the main concept covered in this lesson?',
                            'type' => 'multiple_choice',
                            'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
                            'correct' => 0
                        ]
                    ]
                ]);
            case 'resource':
                return 'This lesson provides downloadable resources and supplementary materials.';
            default:
                return 'This lesson provides comprehensive coverage of the topic with practical examples.';
        }
    }
}
