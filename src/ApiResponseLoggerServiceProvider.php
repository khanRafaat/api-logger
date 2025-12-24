<?php

namespace RafaatAbtahe\ApiResponseLogger;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use RafaatAbtahe\ApiResponseLogger\Http\Middleware\LogApiResponse;
use RafaatAbtahe\ApiResponseLogger\Services\ApiResponseLogService;

class ApiResponseLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/api-response-logger.php',
            'api-response-logger'
        );

        // Register the service as a singleton
        $this->app->singleton(ApiResponseLogService::class, function ($app) {
            return new ApiResponseLogService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/api-response-logger.php' => config_path('api-response-logger.php'),
        ], 'api-response-logger-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/api-response-logger'),
        ], 'api-response-logger-views');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'api-response-logger');

        // Register routes if dashboard is enabled
        if (config('api-response-logger.dashboard.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }

        // Register middleware on API routes
        if (config('api-response-logger.enabled', true)) {
            $this->registerMiddleware();
        }
    }

    /**
     * Register the middleware on API routes.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        // Add middleware to the 'api' middleware group
        $router->pushMiddlewareToGroup('api', LogApiResponse::class);
    }
}
