<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding tags...');

        $tags = [
            // Programming tags
            ['name' => 'JavaScript', 'slug' => 'javascript', 'color' => '#f7df1e'],
            ['name' => 'Python', 'slug' => 'python', 'color' => '#3776ab'],
            ['name' => 'PHP', 'slug' => 'php', 'color' => '#777bb4'],
            ['name' => 'React', 'slug' => 'react', 'color' => '#61dafb'],
            ['name' => 'Vue.js', 'slug' => 'vuejs', 'color' => '#4fc08d'],
            ['name' => 'Laravel', 'slug' => 'laravel', 'color' => '#ff2d20'],
            ['name' => 'Node.js', 'slug' => 'nodejs', 'color' => '#339933'],
            ['name' => 'MySQL', 'slug' => 'mysql', 'color' => '#00758f'],
            ['name' => 'MongoDB', 'slug' => 'mongodb', 'color' => '#47a248'],
            
            // Design tags
            ['name' => 'Figma', 'slug' => 'figma', 'color' => '#f24e1e'],
            ['name' => 'Adobe XD', 'slug' => 'adobe-xd', 'color' => '#ff61f6'],
            ['name' => 'Sketch', 'slug' => 'sketch', 'color' => '#f7b500'],
            ['name' => 'Photoshop', 'slug' => 'photoshop', 'color' => '#31a8ff'],
            ['name' => 'Illustrator', 'slug' => 'illustrator', 'color' => '#ff9a00'],
            
            // Data Science tags
            ['name' => 'TensorFlow', 'slug' => 'tensorflow', 'color' => '#ff6f00'],
            ['name' => 'PyTorch', 'slug' => 'pytorch', 'color' => '#ee4c2c'],
            ['name' => 'Pandas', 'slug' => 'pandas', 'color' => '#150458'],
            ['name' => 'NumPy', 'slug' => 'numpy', 'color' => '#013243'],
            ['name' => 'Jupyter', 'slug' => 'jupyter', 'color' => '#f37626'],
            
            // Business & Marketing tags
            ['name' => 'SEO', 'slug' => 'seo', 'color' => '#4285f4'],
            ['name' => 'Google Analytics', 'slug' => 'google-analytics', 'color' => '#e37400'],
            ['name' => 'Social Media', 'slug' => 'social-media', 'color' => '#1da1f2'],
            ['name' => 'Content Strategy', 'slug' => 'content-strategy', 'color' => '#ff6b6b'],
            ['name' => 'Agile', 'slug' => 'agile', 'color' => '#0052cc'],
            ['name' => 'Scrum', 'slug' => 'scrum', 'color' => '#009639'],
            
            // General tags
            ['name' => 'Beginner', 'slug' => 'beginner', 'color' => '#28a745'],
            ['name' => 'Intermediate', 'slug' => 'intermediate', 'color' => '#ffc107'],
            ['name' => 'Advanced', 'slug' => 'advanced', 'color' => '#dc3545'],
            ['name' => 'Hands-on', 'slug' => 'hands-on', 'color' => '#17a2b8'],
            ['name' => 'Project-based', 'slug' => 'project-based', 'color' => '#6f42c1'],
            ['name' => 'Certification', 'slug' => 'certification', 'color' => '#fd7e14'],
            ['name' => 'Popular', 'slug' => 'popular', 'color' => '#e83e8c'],
            ['name' => 'New', 'slug' => 'new', 'color' => '#20c997'],
        ];

        foreach ($tags as $tag) {
            DB::table('tags')->insert(array_merge($tag, [
                'tenant_id' => 'demo',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ Tags seeded successfully');
    }
}
