<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding courses...');

        // Get instructor and category IDs
        $instructors = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'instructor')
            ->where('users.tenant_id', 'demo')
            ->pluck('users.id')
            ->toArray();

        $categories = DB::table('categories')
            ->where('tenant_id', 'demo')
            ->whereNotNull('parent_id') // Only subcategories
            ->get()
            ->keyBy('slug');

        $tags = DB::table('tags')
            ->where('tenant_id', 'demo')
            ->get()
            ->keyBy('slug');

        $courses = [
            [
                'tenant_id' => 'demo',
                'strapi_course_id' => 'course_001',
                'category_id' => $categories['web-development']->id,
                'instructor_id' => $instructors[0],
                'title' => 'Complete Web Development Bootcamp',
                'slug' => 'complete-web-development-bootcamp',
                'short_description' => 'Learn full-stack web development from scratch with HTML, CSS, JavaScript, React, and Node.js',
                'description' => 'This comprehensive bootcamp covers everything you need to become a professional web developer. Starting with the basics of HTML and CSS, you\'ll progress through JavaScript fundamentals, modern frameworks like React, and backend development with Node.js. By the end of this course, you\'ll have built multiple real-world projects and be ready for a career in web development.',
                'thumbnail_url' => 'https://via.placeholder.com/400x300/2563eb/ffffff?text=Web+Development',
                'level' => 'beginner',
                'status' => 'published',
                'price' => 99.99,
                'estimated_duration_hours' => 40,
                'language' => 'en',
                'is_featured' => true,
                'is_free' => false,
                'sort_order' => 1,
                'published_at' => now()->subDays(30),
                'tags' => ['javascript', 'react', 'nodejs', 'beginner', 'hands-on'],
            ],
            [
                'tenant_id' => 'demo',
                'strapi_course_id' => 'course_002',
                'category_id' => $categories['machine-learning']->id,
                'instructor_id' => $instructors[1],
                'title' => 'Machine Learning with Python',
                'slug' => 'machine-learning-python',
                'short_description' => 'Master machine learning algorithms and techniques using Python, TensorFlow, and scikit-learn',
                'description' => 'Dive deep into the world of machine learning with this comprehensive Python course. You\'ll learn fundamental algorithms, work with real datasets, and build predictive models. Topics include supervised and unsupervised learning, neural networks, and deep learning with TensorFlow.',
                'thumbnail_url' => 'https://via.placeholder.com/400x300/10b981/ffffff?text=Machine+Learning',
                'level' => 'intermediate',
                'status' => 'published',
                'price' => 149.99,
                'estimated_duration_hours' => 35,
                'language' => 'en',
                'is_featured' => true,
                'is_free' => false,
                'sort_order' => 2,
                'published_at' => now()->subDays(25),
                'tags' => ['python', 'tensorflow', 'pandas', 'intermediate', 'project-based'],
            ],
            [
                'tenant_id' => 'demo',
                'strapi_course_id' => 'course_003',
                'category_id' => $categories['ui-ux-design']->id,
                'instructor_id' => $instructors[2],
                'title' => 'UI/UX Design Fundamentals',
                'slug' => 'ui-ux-design-fundamentals',
                'short_description' => 'Learn user interface and user experience design principles with Figma and Adobe XD',
                'description' => 'This course covers the essential principles of UI/UX design. You\'ll learn about user research, wireframing, prototyping, and visual design. Using industry-standard tools like Figma and Adobe XD, you\'ll create beautiful and functional user interfaces.',
                'thumbnail_url' => 'https://via.placeholder.com/400x300/f59e0b/ffffff?text=UI/UX+Design',
                'level' => 'beginner',
                'status' => 'published',
                'price' => 79.99,
                'estimated_duration_hours' => 25,
                'language' => 'en',
                'is_featured' => false,
                'is_free' => false,
                'sort_order' => 3,
                'published_at' => now()->subDays(20),
                'tags' => ['figma', 'adobe-xd', 'beginner', 'hands-on'],
            ],
            [
                'tenant_id' => 'demo',
                'strapi_course_id' => 'course_004',
                'category_id' => $categories['mobile-development']->id,
                'instructor_id' => $instructors[3],
                'title' => 'React Native Mobile App Development',
                'slug' => 'react-native-mobile-development',
                'short_description' => 'Build cross-platform mobile apps for iOS and Android using React Native',
                'description' => 'Learn to build native mobile applications using React Native. This course covers navigation, state management, API integration, and publishing to app stores. You\'ll build several real-world applications throughout the course.',
                'thumbnail_url' => 'https://via.placeholder.com/400x300/8b5cf6/ffffff?text=React+Native',
                'level' => 'intermediate',
                'status' => 'published',
                'price' => 129.99,
                'estimated_duration_hours' => 30,
                'language' => 'en',
                'is_featured' => false,
                'is_free' => false,
                'sort_order' => 4,
                'published_at' => now()->subDays(15),
                'tags' => ['react', 'javascript', 'intermediate', 'project-based'],
            ],
            [
                'tenant_id' => 'demo',
                'strapi_course_id' => 'course_005',
                'category_id' => $categories['digital-marketing']->id,
                'instructor_id' => $instructors[0],
                'title' => 'Digital Marketing Masterclass',
                'slug' => 'digital-marketing-masterclass',
                'short_description' => 'Complete guide to digital marketing including SEO, PPC, social media, and analytics',
                'description' => 'Master all aspects of digital marketing in this comprehensive course. Learn SEO techniques, Google Ads, Facebook advertising, content marketing, email marketing, and analytics. Perfect for entrepreneurs and marketing professionals.',
                'thumbnail_url' => 'https://via.placeholder.com/400x300/ef4444/ffffff?text=Digital+Marketing',
                'level' => 'beginner',
                'status' => 'published',
                'price' => 89.99,
                'estimated_duration_hours' => 28,
                'language' => 'en',
                'is_featured' => true,
                'is_free' => false,
                'sort_order' => 5,
                'published_at' => now()->subDays(10),
                'tags' => ['seo', 'google-analytics', 'social-media', 'beginner', 'certification'],
            ],
            [
                'tenant_id' => 'demo',
                'strapi_course_id' => 'course_006',
                'category_id' => $categories['web-development']->id,
                'instructor_id' => $instructors[2],
                'title' => 'Advanced Laravel Development',
                'slug' => 'advanced-laravel-development',
                'short_description' => 'Master advanced Laravel concepts including testing, performance optimization, and microservices',
                'description' => 'Take your Laravel skills to the next level with advanced topics like automated testing, performance optimization, caching strategies, queue management, and building microservices. This course is perfect for developers with Laravel experience.',
                'thumbnail_url' => 'https://via.placeholder.com/400x300/dc2626/ffffff?text=Advanced+Laravel',
                'level' => 'advanced',
                'status' => 'published',
                'price' => 179.99,
                'estimated_duration_hours' => 45,
                'language' => 'en',
                'is_featured' => false,
                'is_free' => false,
                'sort_order' => 6,
                'published_at' => now()->subDays(5),
                'tags' => ['laravel', 'php', 'mysql', 'advanced', 'hands-on'],
            ],
            [
                'tenant_id' => 'demo',
                'strapi_course_id' => 'course_007',
                'category_id' => $categories['project-management']->id,
                'instructor_id' => $instructors[1],
                'title' => 'Agile Project Management with Scrum',
                'slug' => 'agile-project-management-scrum',
                'short_description' => 'Learn Agile methodologies and Scrum framework for effective project management',
                'description' => 'Master Agile project management and the Scrum framework. This course covers sprint planning, daily standups, retrospectives, and stakeholder management. Perfect for project managers and team leads.',
                'thumbnail_url' => 'https://via.placeholder.com/400x300/059669/ffffff?text=Agile+Scrum',
                'level' => 'intermediate',
                'status' => 'published',
                'price' => 69.99,
                'estimated_duration_hours' => 20,
                'language' => 'en',
                'is_featured' => false,
                'is_free' => false,
                'sort_order' => 7,
                'published_at' => now()->subDays(3),
                'tags' => ['agile', 'scrum', 'intermediate', 'certification'],
            ],
            [
                'tenant_id' => 'demo',
                'strapi_course_id' => 'course_008',
                'category_id' => $categories['data-visualization']->id,
                'instructor_id' => $instructors[3],
                'title' => 'Introduction to Programming',
                'slug' => 'introduction-programming',
                'short_description' => 'Start your programming journey with Python - perfect for complete beginners',
                'description' => 'A gentle introduction to programming concepts using Python. No prior experience needed! Learn variables, loops, functions, and basic problem-solving. This course sets the foundation for any programming career.',
                'thumbnail_url' => 'https://via.placeholder.com/400x300/3b82f6/ffffff?text=Intro+Programming',
                'level' => 'beginner',
                'status' => 'published',
                'price' => 0.00,
                'estimated_duration_hours' => 15,
                'language' => 'en',
                'is_featured' => true,
                'is_free' => true,
                'sort_order' => 8,
                'published_at' => now()->subDay(),
                'tags' => ['python', 'beginner', 'new', 'hands-on'],
            ],
        ];

        $courseIds = [];
        foreach ($courses as $course) {
            $courseTags = $course['tags'];
            unset($course['tags']);
            
            DB::table('courses')->updateOrInsert(
                [
                    'tenant_id' => $course['tenant_id'],
                    'slug' => $course['slug']
                ], // Match condition
                array_merge($course, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
            
            // Get the course ID
            $courseId = DB::table('courses')
                ->where('tenant_id', $course['tenant_id'])
                ->where('slug', $course['slug'])
                ->value('id');
            
            $courseIds[] = $courseId;

            // Add course tags
            foreach ($courseTags as $tagSlug) {
                if (isset($tags[$tagSlug])) {
                    DB::table('course_tags')->updateOrInsert(
                        [
                            'course_id' => $courseId,
                            'tag_id' => $tags[$tagSlug]->id
                        ], // Match condition
                        [
                            'created_at' => now(),
                        ]
                    );
                }
            }

            // Add learning objectives
            $objectives = [
                $courseId => [
                    'Understand core concepts and fundamentals',
                    'Build practical, real-world projects',
                    'Apply best practices and industry standards',
                    'Develop problem-solving skills',
                ]
            ];

            foreach ($objectives[$courseId] as $index => $objective) {
                DB::table('course_learning_objectives')->updateOrInsert(
                    [
                        'course_id' => $courseId,
                        'objective' => $objective
                    ], // Match condition
                    [
                        'sort_order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // Add prerequisites for intermediate/advanced courses
            if (in_array($course['level'], ['intermediate', 'advanced'])) {
                $prerequisites = [
                    'Basic understanding of programming concepts',
                    'Familiarity with command line/terminal',
                    'Computer with required software installed',
                ];

                foreach ($prerequisites as $index => $prerequisite) {
                    DB::table('course_prerequisites')->updateOrInsert(
                        [
                            'course_id' => $courseId,
                            'prerequisite' => $prerequisite
                        ], // Match condition
                        [
                            'sort_order' => $index + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ Courses seeded successfully');
        $this->command->info('Course IDs: ' . implode(', ', $courseIds));
    }
}
