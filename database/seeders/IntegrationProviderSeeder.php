<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntegrationProviderSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding integration providers...');

        // Create integration providers
        $providers = [
            [
                'name' => 'stripe',
                'display_name' => 'Stripe Payments',
                'description' => 'Accept payments and manage subscriptions with Stripe',
                'required_fields' => json_encode([
                    'api_key' => ['type' => 'string', 'required' => true, 'encrypted' => true],
                    'webhook_secret' => ['type' => 'string', 'required' => true, 'encrypted' => true],
                    'test_mode' => ['type' => 'boolean', 'required' => false, 'default' => true]
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'youtube',
                'display_name' => 'YouTube Integration',
                'description' => 'Embed and manage YouTube videos in your courses',
                'required_fields' => json_encode([
                    'api_key' => ['type' => 'string', 'required' => true, 'encrypted' => true],
                    'channel_id' => ['type' => 'string', 'required' => false]
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'box',
                'display_name' => 'Box Storage',
                'description' => 'Store and manage course content with Box',
                'required_fields' => json_encode([
                    'client_id' => ['type' => 'string', 'required' => true],
                    'client_secret' => ['type' => 'string', 'required' => true, 'encrypted' => true],
                    'folder_id' => ['type' => 'string', 'required' => false]
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'strapi',
                'display_name' => 'Strapi CMS',
                'description' => 'Manage course content with Strapi headless CMS',
                'required_fields' => json_encode([
                    'api_url' => ['type' => 'string', 'required' => true],
                    'api_token' => ['type' => 'string', 'required' => true, 'encrypted' => true],
                    'sync_enabled' => ['type' => 'boolean', 'required' => false, 'default' => true]
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'google_gemini',
                'display_name' => 'Google Gemini AI',
                'description' => 'AI-powered content generation and analysis',
                'required_fields' => json_encode([
                    'api_key' => ['type' => 'string', 'required' => true, 'encrypted' => true],
                    'model' => ['type' => 'string', 'required' => false, 'default' => 'gemini-pro'],
                    'max_tokens' => ['type' => 'integer', 'required' => false, 'default' => 1000]
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'zoom',
                'display_name' => 'Zoom Meetings',
                'description' => 'Host live classes and webinars with Zoom',
                'required_fields' => json_encode([
                    'api_key' => ['type' => 'string', 'required' => true, 'encrypted' => true],
                    'api_secret' => ['type' => 'string', 'required' => true, 'encrypted' => true],
                    'webhook_secret' => ['type' => 'string', 'required' => false, 'encrypted' => true]
                ]),
                'is_active' => true,
            ],
        ];

        $providerIds = [];
        foreach ($providers as $provider) {
            $providerRecord = DB::table('integration_providers')->updateOrInsert(
                ['name' => $provider['name']], // Match condition
                array_merge($provider, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
            
            // Get the ID of the provider
            $providerIds[] = DB::table('integration_providers')
                ->where('name', $provider['name'])
                ->value('id');
        }

        // Create tenant integrations for demo tenant
        $integrations = [
            [
                'tenant_id' => 'demo',
                'integration_provider_id' => $providerIds[0], // Stripe
                'configuration' => json_encode([
                    'api_key' => 'sk_test_demo_key',
                    'webhook_secret' => 'whsec_demo_secret',
                    'test_mode' => true
                ]),
                'is_enabled' => true,
            ],
            [
                'tenant_id' => 'demo',
                'integration_provider_id' => $providerIds[1], // YouTube
                'configuration' => json_encode([
                    'api_key' => 'demo_youtube_api_key',
                    'channel_id' => 'UC_demo_channel'
                ]),
                'is_enabled' => true,
            ],
            [
                'tenant_id' => 'demo',
                'integration_provider_id' => $providerIds[3], // Strapi
                'configuration' => json_encode([
                    'api_url' => 'https://demo-strapi.lms.local',
                    'api_token' => 'demo_strapi_token',
                    'sync_enabled' => true
                ]),
                'is_enabled' => true,
            ],
        ];

        foreach ($integrations as $integration) {
            DB::table('tenant_integrations')->updateOrInsert(
                [
                    'tenant_id' => $integration['tenant_id'],
                    'integration_provider_id' => $integration['integration_provider_id']
                ], // Match condition
                array_merge($integration, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Integration providers seeded successfully');
    }
}
