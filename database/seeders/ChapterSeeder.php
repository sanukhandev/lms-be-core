<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                
                DB::table('chapters')->insert([
                    'tenant_id' => 'demo',
                    'module_id' => $module->id,
                    'content_type_id' => $contentType->id,
                    'title' => "Lesson {$i}: " . $this->getChapterTitle($i),
                    'content' => $this->getChapterContent($contentType->name),
                    'video_url' => $contentType->name === 'video' ? 'https://www.youtube.com/watch?v=demo' : null,
                    'duration_minutes' => rand(10, 30),
                    'sort_order' => $i,
                    'is_published' => true,
                    'is_free_preview' => $i === 1, // First lesson is free preview
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Chapters seeded successfully');
    }

    private function getRandomContentType($contentTypes)
    {
        $types = ['video', 'article', 'quiz', 'pdf'];
        $randomType = $types[array_rand($types)];
        return $contentTypes[$randomType];
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
            case 'article':
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
            default:
                return 'This lesson provides comprehensive coverage of the topic with practical examples.';
        }
    }
}
