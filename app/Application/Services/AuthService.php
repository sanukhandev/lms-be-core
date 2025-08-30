<?php

namespace App\Application\Services;

use App\Domain\User\User;
use App\Domain\Tenant\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate user and return JWT token.
     */
    public function authenticate(string $email, string $password, ?string $tenantId = null): array
    {
        $user = User::where('email', $email);
        
        if ($tenantId) {
            $user->where('tenant_id', $tenantId);
        }
        
        $user = $user->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact support.'],
            ]);
        }

        // Initialize tenancy context
        if ($user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            if ($tenant && $tenant->isActive()) {
                tenancy()->initialize($tenant);
            }
        }

        $token = JWTAuth::fromUser($user);
        $user->updateLastLogin();

        return [
            'user' => $user->load('roles'),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'tenant' => $user->tenant,
        ];
    }

    /**
     * Register a new user.
     */
    public function register(array $userData): array
    {
        return DB::transaction(function () use ($userData) {
            $user = User::create([
                'tenant_id' => $userData['tenant_id'] ?? null,
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'phone' => $userData['phone'] ?? null,
                'is_active' => true,
            ]);

            // Assign default role
            $role = $userData['role'] ?? 'student';
            $user->assignRole($role);

            $token = JWTAuth::fromUser($user);

            return [
                'user' => $user->load('roles'),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ];
        });
    }

    /**
     * Refresh JWT token.
     */
    public function refreshToken(): array
    {
        try {
            $token = JWTAuth::refresh();
            $user = JWTAuth::setToken($token)->toUser();

            return [
                'user' => $user->load('roles'),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ];
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'token' => ['Token could not be refreshed.'],
            ]);
        }
    }

    /**
     * Logout user (invalidate token).
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Get authenticated user with tenant context.
     */
    public function getAuthenticatedUser(): ?User
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if ($user && $user->tenant_id) {
                $tenant = Tenant::find($user->tenant_id);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Super admin impersonation.
     */
    public function impersonate(int $userId): array
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->isSuperAdmin()) {
            throw ValidationException::withMessages([
                'permission' => ['Only super admins can impersonate users.'],
            ]);
        }

        $targetUser = User::findOrFail($userId);
        
        // Initialize target user's tenant context
        if ($targetUser->tenant_id) {
            $tenant = Tenant::find($targetUser->tenant_id);
            if ($tenant) {
                tenancy()->initialize($tenant);
            }
        }

        $token = JWTAuth::fromUser($targetUser);

        return [
            'user' => $targetUser->load('roles'),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'impersonated_by' => $currentUser->id,
            'tenant' => $targetUser->tenant,
        ];
    }
}
