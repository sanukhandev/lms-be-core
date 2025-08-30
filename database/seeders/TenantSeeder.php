<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding tenants and domains...');

        // Create demo tenant (skip if exists)
        if (!DB::table('tenants')->where('id', 'demo')->exists()) {
            DB::table('tenants')->insert([
                'id' => 'demo',
                'data' => json_encode([
                    'name' => 'Demo University',
                    'status' => 'active',
                    'subscription_type' => 'premium',
                    'settings' => [
                        'timezone' => 'UTC',
                        'currency' => 'USD',
                        'language' => 'en'
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Demo tenant created');
        } else {
            $this->command->info('✓ Demo tenant already exists, skipping...');
        }

        // Create domain for demo tenant (skip if exists)
        if (!DB::table('domains')->where('domain', 'demo.lms.local')->exists()) {
            DB::table('domains')->insert([
                'domain' => 'demo.lms.local',
                'tenant_id' => 'demo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Demo domain created');
        } else {
            $this->command->info('✓ Demo domain already exists, skipping...');
        }

        // Create tenant settings (skip if exists)
        if (!DB::table('tenant_settings')->where('tenant_id', 'demo')->exists()) {
            DB::table('tenant_settings')->insert([
                'tenant_id' => 'demo',
                'organization_name' => 'Demo University',
                'domain' => 'demo.lms.local',
                'contact_email' => 'admin@demo.lms',
                'contact_phone' => '+1-555-0100',
                'timezone' => 'UTC',
                'language' => 'en',
                'currency' => 'USD',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Tenant settings created');
        } else {
            $this->command->info('✓ Tenant settings already exist, skipping...');
        }

        // Create tenant branding (skip if exists)
        if (!DB::table('tenant_branding')->where('tenant_id', 'demo')->exists()) {
            DB::table('tenant_branding')->insert([
                'tenant_id' => 'demo',
                'logo_url' => 'https://via.placeholder.com/200x60/2563eb/ffffff?text=Demo+University',
                'favicon_url' => 'https://via.placeholder.com/32x32/2563eb/ffffff?text=DU',
                'primary_color' => '#2563eb',
                'secondary_color' => '#1e40af',
                'accent_color' => '#10b981',
                'background_color' => '#ffffff',
                'text_color' => '#1f2937',
                'custom_css' => '/* Custom styles for Demo University */',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Tenant branding created');
        } else {
            $this->command->info('✓ Tenant branding already exists, skipping...');
        }

        $this->command->info('✅ Tenants seeded successfully');
    }
}
