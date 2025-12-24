<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Logging
    |--------------------------------------------------------------------------
    |
    | Set to false to disable API response time logging entirely.
    |
    */
    'enabled' => env('API_RESPONSE_LOGGER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Log File Path
    |--------------------------------------------------------------------------
    |
    | The path where API response logs will be stored.
    |
    */
    'log_path' => storage_path('logs/api_response_logs.json'),

    /*
    |--------------------------------------------------------------------------
    | Excluded Routes
    |--------------------------------------------------------------------------
    |
    | Routes that should be excluded from logging. These are exact path matches.
    |
    */
    'excluded_routes' => [
        '/',
        '/api-status',
        '/api-stats',
        '/docs',
        '/api/documentation',
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Retention Hours
    |--------------------------------------------------------------------------
    |
    | How many hours to keep log entries. Older entries will be automatically
    | deleted during the next log write.
    |
    */
    'retention_hours' => env('API_RESPONSE_LOGGER_RETENTION', 24),

    /*
    |--------------------------------------------------------------------------
    | Dashboard Settings
    |--------------------------------------------------------------------------
    |
    | Configure the monitoring dashboard appearance and behavior.
    |
    */
    'dashboard' => [
        // Enable/disable the dashboard route (replaces welcome page)
        'enabled' => env('API_RESPONSE_LOGGER_DASHBOARD', true),

        // Dashboard branding
        'app_name' => env('APP_NAME', 'API Response Monitor'),
        'subtitle' => 'API Server',

        // Developer credit (set to null to hide)
        'developer' => [
            'name' => 'ATI Limited',
            'url' => 'https://atilimited.net',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Slow Request Threshold (ms)
    |--------------------------------------------------------------------------
    |
    | Requests taking longer than this threshold will be marked as "slow".
    |
    */
    'slow_threshold' => env('API_RESPONSE_LOGGER_SLOW_THRESHOLD', 500),
];
