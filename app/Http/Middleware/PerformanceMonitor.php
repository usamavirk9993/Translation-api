<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $route = $request->route();
        $routeName = $route ? $route->getName() : 'unknown';

        // Log performance metrics
        Log::info('API Performance', [
            'route' => $routeName,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'execution_time_ms' => round($executionTime, 2),
            'user_id' => $request->user()?->id,
        ]);

        // Add performance header to response
        $response->headers->set('X-Execution-Time', round($executionTime, 2).'ms');

        // Check if performance requirements are met
        $this->checkPerformanceRequirements($executionTime, $routeName);

        return $response;
    }

    private function checkPerformanceRequirements(float $executionTime, string $routeName): void
    {
        $exportRoutes = ['translations.export'];
        $maxTime = in_array($routeName, $exportRoutes) ? 500 : 200; // 500ms for export, 200ms for others

        if ($executionTime > $maxTime) {
            Log::warning('Performance requirement not met', [
                'route' => $routeName,
                'execution_time_ms' => round($executionTime, 2),
                'max_allowed_ms' => $maxTime,
            ]);
        }
    }
}
