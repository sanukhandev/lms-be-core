<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding categories...');

        // Main categories
        $categories = [
            [
                'tenant_id' => 'demo',
                'parent_id' => null,
                'name' => 'Programming',
                'slug' => 'programming',
                'description' => 'Learn various programming languages and development frameworks',
                'icon' => 'code',
                'color' => '#3B82F6',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => null,
                'name' => 'Data Science',
                'slug' => 'data-science',
                'description' => 'Master data analysis, machine learning, and AI',
                'icon' => 'chart-bar',
                'color' => '#10B981',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => null,
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'UI/UX design, graphic design, and creative skills',
                'icon' => 'palette',
                'color' => '#F59E0B',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => null,
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business skills, entrepreneurship, and management',
                'icon' => 'briefcase',
                'color' => '#8B5CF6',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => null,
                'name' => 'Marketing',
                'slug' => 'marketing',
                'description' => 'Digital marketing, SEO, and social media strategies',
                'icon' => 'megaphone',
                'color' => '#EF4444',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        $mainCategoryIds = [];
        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                [
                    'tenant_id' => $category['tenant_id'],
                    'slug' => $category['slug']
                ], // Match condition
                array_merge($category, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
            
            // Get the ID of the category
            $mainCategoryIds[] = DB::table('categories')
                ->where('tenant_id', $category['tenant_id'])
                ->where('slug', $category['slug'])
                ->value('id');
        }

        // Subcategories
        $subcategories = [
            // Programming subcategories
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[0],
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Frontend and backend web development',
                'sort_order' => 1,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[0],
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'iOS, Android, and cross-platform mobile apps',
                'sort_order' => 2,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[0],
                'name' => 'Game Development',
                'slug' => 'game-development',
                'description' => 'Video game programming and design',
                'sort_order' => 3,
            ],

            // Data Science subcategories
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[1],
                'name' => 'Machine Learning',
                'slug' => 'machine-learning',
                'description' => 'ML algorithms and deep learning',
                'sort_order' => 1,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[1],
                'name' => 'Data Visualization',
                'slug' => 'data-visualization',
                'description' => 'Charts, graphs, and data presentation',
                'sort_order' => 2,
            ],

            // Design subcategories
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[2],
                'name' => 'UI/UX Design',
                'slug' => 'ui-ux-design',
                'description' => 'User interface and experience design',
                'sort_order' => 1,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[2],
                'name' => 'Graphic Design',
                'slug' => 'graphic-design',
                'description' => 'Visual design and branding',
                'sort_order' => 2,
            ],

            // Business subcategories
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[3],
                'name' => 'Project Management',
                'slug' => 'project-management',
                'description' => 'Agile, Scrum, and project planning',
                'sort_order' => 1,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[3],
                'name' => 'Leadership',
                'slug' => 'leadership',
                'description' => 'Team management and leadership skills',
                'sort_order' => 2,
            ],

            // Marketing subcategories
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[4],
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'description' => 'Online marketing strategies and tools',
                'sort_order' => 1,
            ],
            [
                'tenant_id' => 'demo',
                'parent_id' => $mainCategoryIds[4],
                'name' => 'Content Marketing',
                'slug' => 'content-marketing',
                'description' => 'Content creation and marketing strategies',
                'sort_order' => 2,
            ],
        ];

        foreach ($subcategories as $subcategory) {
            DB::table('categories')->updateOrInsert(
                [
                    'tenant_id' => $subcategory['tenant_id'],
                    'slug' => $subcategory['slug']
                ], // Match condition
                array_merge($subcategory, [
                    'icon' => null,
                    'color' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Categories seeded successfully');
    }
}
