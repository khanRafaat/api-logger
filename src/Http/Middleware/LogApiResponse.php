<?php

namespace RafaatAbtahe\ApiResponseLogger\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class LogApiResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if logging is disabled
        if (!config('api-response-logger.enabled', true)) {
            return $next($request);
        }

        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Only log API routes, exclude configured endpoints
        if ($this->shouldLog($request)) {
            $this->logResponseTime($request, $responseTime, $response->getStatusCode());
        }

        return $response;
    }

    /**
     * Check if the request should be logged.
     */
    private function shouldLog(Request $request): bool
    {
        $path = $request->getPathInfo();
        $excludedRoutes = config('api-response-logger.excluded_routes', []);

        // Exclude specific routes
        if (in_array($path, $excludedRoutes)) {
            return false;
        }

        // Only log API routes (paths starting with /api)
        return str_starts_with($path, '/api');
    }

    /**
     * Log response time to a JSON file.
     */
    private function logResponseTime(Request $request, float $responseTime, int $statusCode): void
    {
        $logFile = config('api-response-logger.log_path', storage_path('logs/api_response_logs.json'));
        $retentionHours = config('api-response-logger.retention_hours', 24);

        $logs = [];
        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $logs = json_decode($content, true) ?? [];
        }

        // Add new log entry
        $logs[] = [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'endpoint' => $request->getPathInfo(),
            'method' => $request->method(),
            'response_time_ms' => round($responseTime, 2),
            'status_code' => $statusCode,
        ];

        // Filter to keep only logs within retention period
        $cutoffTime = now()->subHours($retentionHours)->format('Y-m-d H:i:s');
        $logs = array_filter($logs, function ($log) use ($cutoffTime) {
            return $log['timestamp'] >= $cutoffTime;
        });

        // Re-index array after filtering
        $logs = array_values($logs);

        // Ensure directory exists
        $dir = dirname($logFile);
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        // Write back to file
        File::put($logFile, json_encode($logs, JSON_PRETTY_PRINT));
    }
}
