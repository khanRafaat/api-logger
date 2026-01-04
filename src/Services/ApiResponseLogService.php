<?php

namespace RafaatAbtahe\ApiResponseLogger\Services;

use Illuminate\Support\Facades\File;

class ApiResponseLogService
{
    protected string $logFile;
    protected int $retentionHours;

    public function __construct()
    {
        $this->logFile = config('api-response-logger.log_path', storage_path('logs/api_response_logs.json'));
        $this->retentionHours = config('api-response-logger.retention_hours', 24);
    }

    /**
     * Get all logs from the JSON file (only last 24 hours)
     */
    public function getLogs(): array
    {
        if (!File::exists($this->logFile)) {
            return [];
        }

        $content = File::get($this->logFile);
        $logs = json_decode($content, true);

        if (!is_array($logs)) {
            return [];
        }

        // Filter to keep only logs within retention period (last 24 hours)
        $cutoffTime = now()->subHours($this->retentionHours)->format('Y-m-d H:i:s');
        $logs = array_filter($logs, function ($log) use ($cutoffTime) {
            return isset($log['timestamp']) && $log['timestamp'] >= $cutoffTime;
        });

        return array_values($logs);
    }

    /**
     * Cleanup old logs (older than 24 hours) and persist to file
     */
    public function cleanupOldLogs(): int
    {
        if (!File::exists($this->logFile)) {
            return 0;
        }

        $content = File::get($this->logFile);
        $logs = json_decode($content, true);

        if (!is_array($logs)) {
            return 0;
        }

        $originalCount = count($logs);

        // Filter to keep only logs within retention period
        $cutoffTime = now()->subHours($this->retentionHours)->format('Y-m-d H:i:s');
        $logs = array_filter($logs, function ($log) use ($cutoffTime) {
            return isset($log['timestamp']) && $log['timestamp'] >= $cutoffTime;
        });

        $logs = array_values($logs);
        $deletedCount = $originalCount - count($logs);

        // Write cleaned logs back to file
        File::put($this->logFile, json_encode($logs, JSON_PRETTY_PRINT));

        return $deletedCount;
    }

    /**
     * Get statistics for the configured retention period
     */
    public function getStats(): array
    {
        // Automatically cleanup old logs (older than 24 hours)
        $this->cleanupOldLogs();

        $logs = $this->getLogs();

        if (empty($logs)) {
            return [
                'total_requests' => 0,
                'average_response_time' => 0,
                'max_response_time' => 0,
                'min_response_time' => 0,
                'hourly_data' => $this->getEmptyHourlyData(),
                'requests' => [],
            ];
        }

        $responseTimes = array_column($logs, 'response_time_ms');

        // Calculate hourly data for the chart
        $hourlyData = $this->calculateHourlyData($logs);

        // Format requests for chart (limit to last 100)
        $requests = array_slice(array_map(function ($log) {
            return [
                'timestamp' => $log['timestamp'],
                'response_time' => $log['response_time_ms'],
                'endpoint' => $log['endpoint'],
            ];
        }, $logs), -100);

        return [
            'total_requests' => count($logs),
            'average_response_time' => round(array_sum($responseTimes) / count($responseTimes), 2),
            'max_response_time' => max($responseTimes),
            'min_response_time' => min($responseTimes),
            'hourly_data' => $hourlyData,
            'requests' => $requests,
        ];
    }

    /**
     * Get empty hourly data structure
     */
    protected function getEmptyHourlyData(): array
    {
        $retentionHours = config('api-response-logger.retention_hours', 24);
        $hourlyData = [];

        for ($i = $retentionHours - 1; $i >= 0; $i--) {
            $hour = now()->subHours($i);
            $hourlyData[] = [
                'hour' => $hour->format('H:00'),
                'avg' => 0,
                'max' => 0,
            ];
        }
        return $hourlyData;
    }

    /**
     * Calculate hourly statistics for chart
     */
    protected function calculateHourlyData(array $logs): array
    {
        $retentionHours = config('api-response-logger.retention_hours', 24);
        $hourlyData = [];

        // Initialize hours based on retention period
        for ($i = $retentionHours - 1; $i >= 0; $i--) {
            $hour = now()->subHours($i);
            $hourKey = $hour->format('Y-m-d H');
            $hourlyData[$hourKey] = [
                'hour' => $hour->format('H:00'),
                'avg' => 0,
                'max' => 0,
                'count' => 0,
                'total' => 0,
            ];
        }

        // Aggregate logs by hour
        foreach ($logs as $log) {
            $logTime = \Carbon\Carbon::parse($log['timestamp']);
            $hourKey = $logTime->format('Y-m-d H');

            if (isset($hourlyData[$hourKey])) {
                $hourlyData[$hourKey]['count']++;
                $hourlyData[$hourKey]['total'] += $log['response_time_ms'];
                $hourlyData[$hourKey]['max'] = max($hourlyData[$hourKey]['max'], $log['response_time_ms']);
            }
        }

        // Calculate averages
        foreach ($hourlyData as &$data) {
            if ($data['count'] > 0) {
                $data['avg'] = round($data['total'] / $data['count'], 2);
            }
            unset($data['total'], $data['count']);
        }

        return array_values($hourlyData);
    }
}
