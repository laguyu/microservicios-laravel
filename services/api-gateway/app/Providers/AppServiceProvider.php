<?php

namespace App\Providers;

use Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        config()->set('scramble.cache.store', 'array');
        config()->set('scramble.security_strategy', [
            MiddlewareAuthSecurityStrategy::class,
            [
                'middleware' => ['jwt'],
                'scheme' => SecurityScheme::http('bearer', 'JWT'),
            ],
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewApiDocs', fn (?Authenticatable $user = null): bool => true);
    }
}
