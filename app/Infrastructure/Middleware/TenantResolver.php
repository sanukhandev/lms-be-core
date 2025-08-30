<?php

namespace App\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedException;

class TenantResolver extends InitializeTenancyByDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Try to resolve tenant by domain first
            return parent::handle($request, $next);
        } catch (TenantCouldNotBeIdentifiedException $e) {
            // Fallback to API key or JWT token resolution
            return $this->resolveTenantByApiKey($request, $next);
        }
    }

    /**
     * Resolve tenant by API key or JWT token.
     */
    protected function resolveTenantByApiKey(Request $request, Closure $next): Response
    {
        // Try to get tenant from X-Tenant-ID header
        $tenantId = $request->header('X-Tenant-ID');
        
        if (!$tenantId) {
            // Try to extract from JWT token
            $tenantId = $this->extractTenantFromJWT($request);
        }

        if ($tenantId) {
            $tenant = \App\Domain\Tenant\Tenant::find($tenantId);
            
            if ($tenant) {
                tenancy()->initialize($tenant);
                return $next($request);
            }
        }

        return response()->json([
            'error' => 'Tenant could not be identified',
            'message' => 'Please provide a valid domain, X-Tenant-ID header, or JWT token with tenant information.'
        ], 400);
    }

    /**
     * Extract tenant ID from JWT token.
     */
    protected function extractTenantFromJWT(Request $request): ?string
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return null;
            }

            // Use JWT auth to parse token and extract tenant_id
            $payload = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->getPayload();
            
            return $payload->get('tenant_id');
        } catch (\Exception $e) {
            return null;
        }
    }
}
