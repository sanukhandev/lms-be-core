<?php

namespace App\Infrastructure\ExternalServices;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class StrapiService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->baseUrl = config('services.strapi.url');
        $this->apiToken = config('services.strapi.token');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Get all courses from Strapi.
     */
    public function getCourses(array $params = []): array
    {
        try {
            $cacheKey = 'strapi_courses_' . md5(serialize($params));
            
            return Cache::remember($cacheKey, 300, function () use ($params) {
                $response = $this->client->get('/api/courses', [
                    'query' => array_merge([
                        'populate' => '*',
                        'pagination[limit]' => 100,
                    ], $params)
                ]);

                return json_decode($response->getBody()->getContents(), true);
            });
        } catch (GuzzleException $e) {
            Log::error('Strapi API error: ' . $e->getMessage());
            throw new \Exception('Failed to fetch courses from Strapi: ' . $e->getMessage());
        }
    }

    /**
     * Get a specific course by ID.
     */
    public function getCourse(int $id): array
    {
        try {
            $cacheKey = "strapi_course_{$id}";
            
            return Cache::remember($cacheKey, 300, function () use ($id) {
                $response = $this->client->get("/api/courses/{$id}", [
                    'query' => [
                        'populate' => 'deep',
                    ]
                ]);

                return json_decode($response->getBody()->getContents(), true);
            });
        } catch (GuzzleException $e) {
            Log::error("Strapi API error fetching course {$id}: " . $e->getMessage());
            throw new \Exception("Failed to fetch course {$id} from Strapi: " . $e->getMessage());
        }
    }

    /**
     * Get course modules with chapters.
     */
    public function getCourseModules(int $courseId): array
    {
        try {
            $cacheKey = "strapi_course_modules_{$courseId}";
            
            return Cache::remember($cacheKey, 300, function () use ($courseId) {
                $response = $this->client->get('/api/modules', [
                    'query' => [
                        'filters[course][id][$eq]' => $courseId,
                        'populate' => 'chapters',
                        'sort' => 'order:asc',
                    ]
                ]);

                return json_decode($response->getBody()->getContents(), true);
            });
        } catch (GuzzleException $e) {
            Log::error("Strapi API error fetching modules for course {$courseId}: " . $e->getMessage());
            throw new \Exception("Failed to fetch modules from Strapi: " . $e->getMessage());
        }
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, config('services.strapi.webhook_secret'));
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Clear cache for specific content.
     */
    public function clearCache(string $contentType, ?int $id = null): void
    {
        if ($id) {
            Cache::forget("strapi_{$contentType}_{$id}");
        }
        
        // Clear list caches
        $pattern = "strapi_{$contentType}s_*";
        $keys = Cache::store()->getRedis()->keys($pattern);
        
        foreach ($keys as $key) {
            Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
        }
    }
}
