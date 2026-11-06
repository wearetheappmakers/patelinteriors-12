<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind Laravel 5.8 format LengthAwarePaginator (Decision 7)
        $this->app->bind(
            \Illuminate\Pagination\LengthAwarePaginator::class,
            \App\Pagination\LengthAwarePaginator::class
        );

        // Bind custom UrlGenerator for exact Laravel 5.8 parameter replacement (Decision 8)
        $this->app->extend('url', function ($url, $app) {
            $routes = $app['router']->getRoutes();
            $custom = new \App\Routing\UrlGenerator(
                $routes,
                $app['request'],
                $app['config']['app.asset_url'] ?? null
            );
            $custom->setSessionResolver(function () use ($app) {
                return $app['session'] ?? null;
            });
            $custom->setKeyResolver(function () use ($app) {
                return $app['config']['app.key'];
            });
            return $custom;
        });

        // Ensure JWT claim factory respects null TTL (never-expiring tokens as configured in config/jwt.php)
        $this->app->extend('tymon.jwt.claim.factory', function ($factory, $app) {
            if ($app['config']['jwt.ttl'] === null) {
                $ref = new \ReflectionProperty($factory, 'ttl');
                $ref->setValue($factory, null);
            }
            return $factory;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFour();
    }
}
