<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register service bindings
        $this->app->bind(
            \App\Application\Services\AuthService::class,
            \App\Application\Services\AuthService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL
        Schema::defaultStringLength(191);
        
        // Configure JWT Auth guard
        config(['auth.defaults.guard' => 'api']);
        config(['auth.guards.api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ]]);
        
        config(['auth.providers.users.model' => \App\Domain\User\User::class]);
    }
}
