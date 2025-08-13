<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyByDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Extract subdomain from host
        $subdomain = $this->getSubdomain($host);
        
        if ($subdomain && $subdomain !== 'www') {
            $tenant = Tenant::where('domain', $subdomain)->first();
            
            if ($tenant) {
                $tenant->makeCurrent();
            } else {
                // Tenant not found, redirect to main domain or show error
                return response()->view('tenant.not-found', [], 404);
            }
        }

        return $next($request);
    }

    /**
     * Extract subdomain from host
     */
    private function getSubdomain(string $host): ?string
    {
        $parts = explode('.', $host);
        
        // Handle localhost development environment
        if (str_contains($host, 'localhost')) {
            if (count($parts) >= 2 && $parts[0] !== 'localhost') {
                return $parts[0];
            }
            return null;
        }
        
        // If IP, no subdomain
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return null;
        }
        
        // For production domains, need at least 3 parts
        if (count($parts) < 3) {
            return null;
        }
        
        return $parts[0];
    }
}

