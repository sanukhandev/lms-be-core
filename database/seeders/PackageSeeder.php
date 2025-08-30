<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding packages and subscriptions...');

        // Create packages
        $packages = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for small organizations getting started with online learning',
                'price' => 29.99,
                'billing_interval' => 'monthly',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Advanced features for growing educational institutions',
                'price' => 79.99,
                'billing_interval' => 'monthly',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Complete solution for large organizations with unlimited features',
                'price' => 199.99,
                'billing_interval' => 'monthly',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        $packageIds = [];
        foreach ($packages as $package) {
            $existingPackage = DB::table('packages')->where('name', $package['name'])->first();
            if (!$existingPackage) {
                $packageIds[] = DB::table('packages')->insertGetId(array_merge($package, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $this->command->info("✓ Package '{$package['name']}' created");
            } else {
                $packageIds[] = $existingPackage->id;
                $this->command->info("✓ Package '{$package['name']}' already exists, skipping...");
            }
        }

        // Create package features
        $features = [
            // Starter package features
            [$packageIds[0], 'max_courses', 'Maximum number of courses allowed', 'integer', '10'],
            [$packageIds[0], 'max_students', 'Maximum number of students allowed', 'integer', '100'],
            [$packageIds[0], 'custom_branding', 'Allow custom branding', 'boolean', 'false'],
            [$packageIds[0], 'advanced_analytics', 'Access to advanced analytics', 'boolean', 'false'],
            [$packageIds[0], 'api_access', 'API access enabled', 'boolean', 'false'],
            [$packageIds[0], 'white_label', 'White label solution', 'boolean', 'false'],

            // Professional package features
            [$packageIds[1], 'max_courses', 'Maximum number of courses allowed', 'integer', '50'],
            [$packageIds[1], 'max_students', 'Maximum number of students allowed', 'integer', '1000'],
            [$packageIds[1], 'custom_branding', 'Allow custom branding', 'boolean', 'true'],
            [$packageIds[1], 'advanced_analytics', 'Access to advanced analytics', 'boolean', 'true'],
            [$packageIds[1], 'api_access', 'API access enabled', 'boolean', 'true'],
            [$packageIds[1], 'white_label', 'White label solution', 'boolean', 'false'],

            // Enterprise package features
            [$packageIds[2], 'max_courses', 'Maximum number of courses allowed', 'string', 'unlimited'],
            [$packageIds[2], 'max_students', 'Maximum number of students allowed', 'string', 'unlimited'],
            [$packageIds[2], 'custom_branding', 'Allow custom branding', 'boolean', 'true'],
            [$packageIds[2], 'advanced_analytics', 'Access to advanced analytics', 'boolean', 'true'],
            [$packageIds[2], 'api_access', 'API access enabled', 'boolean', 'true'],
            [$packageIds[2], 'white_label', 'White label solution', 'boolean', 'true'],
        ];

        foreach ($features as $feature) {
            if (!DB::table('package_features')
                ->where('package_id', $feature[0])
                ->where('feature_name', $feature[1])
                ->exists()) {
                DB::table('package_features')->insert([
                    'package_id' => $feature[0],
                    'feature_name' => $feature[1],
                    'feature_description' => $feature[2],
                    'feature_type' => $feature[3],
                    'feature_value' => $feature[4],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create package quotas
        $quotas = [
            // Starter quotas
            [$packageIds[0], 'max_courses', 'Maximum number of courses', 10, 'count'],
            [$packageIds[0], 'max_students', 'Maximum number of students', 100, 'count'],
            [$packageIds[0], 'storage_limit', 'Storage space limit', 5, 'gb'],
            [$packageIds[0], 'bandwidth_limit', 'Monthly bandwidth limit', 50, 'gb'],

            // Professional quotas
            [$packageIds[1], 'max_courses', 'Maximum number of courses', 50, 'count'],
            [$packageIds[1], 'max_students', 'Maximum number of students', 1000, 'count'],
            [$packageIds[1], 'storage_limit', 'Storage space limit', 25, 'gb'],
            [$packageIds[1], 'bandwidth_limit', 'Monthly bandwidth limit', 250, 'gb'],

            // Enterprise quotas (high limits for "unlimited")
            [$packageIds[2], 'max_courses', 'Maximum number of courses', 999999, 'count'],
            [$packageIds[2], 'max_students', 'Maximum number of students', 999999, 'count'],
            [$packageIds[2], 'storage_limit', 'Storage space limit', 999999, 'gb'],
            [$packageIds[2], 'bandwidth_limit', 'Monthly bandwidth limit', 999999, 'gb'],
        ];

        foreach ($quotas as $quota) {
            if (!DB::table('package_quotas')
                ->where('package_id', $quota[0])
                ->where('quota_name', $quota[1])
                ->exists()) {
                DB::table('package_quotas')->insert([
                    'package_id' => $quota[0],
                    'quota_name' => $quota[1],
                    'quota_description' => $quota[2],
                    'quota_limit' => $quota[3],
                    'quota_unit' => $quota[4],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create subscription for demo tenant
        $subscriptionId = DB::table('subscriptions')->insertGetId([
            'tenant_id' => 'demo',
            'package_id' => $packageIds[1], // Professional package
            'stripe_subscription_id' => 'sub_demo_' . uniqid(),
            'stripe_customer_id' => 'cus_demo_' . uniqid(),
            'status' => 'active',
            'amount' => 79.99,
            'currency' => 'USD',
            'trial_ends_at' => null,
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
            'cancelled_at' => null,
            'expires_at' => now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create subscription usage tracking
        $usageTypes = ['max_courses', 'max_students', 'storage_limit', 'bandwidth_limit'];
        foreach ($usageTypes as $type) {
            if (!DB::table('subscription_usage')
                ->where('subscription_id', $subscriptionId)
                ->where('quota_name', $type)
                ->exists()) {
                DB::table('subscription_usage')->insert([
                    'subscription_id' => $subscriptionId,
                    'quota_name' => $type,
                    'current_usage' => 0,
                    'last_updated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Packages and subscriptions seeded successfully');
    }
}
