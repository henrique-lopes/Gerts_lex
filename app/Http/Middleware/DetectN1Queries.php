<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DetectN1Queries
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $queryCount = 0;
        $queries = [];

        // Only enable in development environment
        if (app()->environment('local', 'development')) {
            DB::listen(function ($query) use (&$queryCount, &$queries) {
                $queryCount++;
                $queries[] = [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ];
            });
        }

        $response = $next($request);

        // Log if too many queries detected
        if (app()->environment('local', 'development') && $queryCount > 10) {
            Log::warning("Potential N+1 Query detected", [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'query_count' => $queryCount,
                'queries' => array_slice($queries, 0, 5), // Log first 5 queries
            ]);

            // Add header for debugging
            $response->headers->set('X-Query-Count', $queryCount);
        }

        return $response;
    }
}
