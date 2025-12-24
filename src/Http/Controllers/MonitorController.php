<?php

namespace RafaatAbtahe\ApiResponseLogger\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use RafaatAbtahe\ApiResponseLogger\Services\ApiResponseLogService;

class MonitorController extends Controller
{
    protected ApiResponseLogService $logService;

    public function __construct(ApiResponseLogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Display the monitoring dashboard
     */
    public function dashboard(): View
    {
        return view('api-response-logger::welcome');
    }

    /**
     * Check if the API server is online
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'online' => true,
            'timestamp' => now()->toIso8601String(),
            'server' => config('api-response-logger.dashboard.app_name', 'API Server'),
        ]);
    }

    /**
     * Get API response statistics
     */
    public function stats(): JsonResponse
    {
        $stats = $this->logService->getStats();

        return response()->json($stats);
    }
}
