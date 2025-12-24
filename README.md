# API Logger

A Laravel package for API response time logging and monitoring dashboard.

## Features

-   📊 **Response Time Logging**: Automatically logs all API response times
-   📈 **Beautiful Dashboard**: Modern monitoring dashboard with charts
-   ⚡ **Real-time Stats**: Average, min, max response times
-   🔧 **Configurable**: Customize excluded routes, retention period, branding
-   🚀 **Zero Configuration**: Works out of the box with sensible defaults

## Installation

### Via Composer

Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/rafaatabtahe/api-logger"
        }
    ]
}
```

Then require the package:

```bash
composer require rafaatabtahe/api-logger
```

### Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=api-response-logger-config
```

### Publish Views (Optional)

```bash
php artisan vendor:publish --tag=api-response-logger-views
```

## Configuration

The configuration file `config/api-response-logger.php` allows you to customize:

```php
return [
    // Enable/disable logging
    'enabled' => true,

    // Log file path
    'log_path' => storage_path('logs/api_response_logs.json'),

    // Routes to exclude from logging
    'excluded_routes' => [
        '/',
        '/api-status',
        '/api-stats',
    ],

    // Log retention in hours
    'retention_hours' => 24,

    // Dashboard settings
    'dashboard' => [
        'enabled' => true,
        'app_name' => 'My API',
        'subtitle' => 'API Server',
        'developer' => [
            'name' => 'Your Name',
            'url' => 'https://your-site.com',
        ],
    ],

    // Slow request threshold in ms
    'slow_threshold' => 500,
];
```

## Environment Variables

```env
API_RESPONSE_LOGGER_ENABLED=true
API_RESPONSE_LOGGER_RETENTION=24
API_RESPONSE_LOGGER_DASHBOARD=true
API_RESPONSE_LOGGER_SLOW_THRESHOLD=500
```

## Routes

The package registers the following routes:

| Route         | Description              |
| ------------- | ------------------------ |
| `/`           | Monitoring dashboard     |
| `/api-status` | JSON status endpoint     |
| `/api-stats`  | JSON statistics endpoint |

## License

MIT License

## Author

Rafaat Abtahe
