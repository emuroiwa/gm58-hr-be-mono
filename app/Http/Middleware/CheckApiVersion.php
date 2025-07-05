<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiVersion
{
    /**
     * Supported API versions
     */
    private array $supportedVersions = ['v1'];

    /**
     * Handle an incoming request.
     * Validates API version from header or URL.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $version = $this->getApiVersion($request);

        if (!in_array($version, $this->supportedVersions)) {
            return response()->json([
                'message' => 'Unsupported API version',
                'supported_versions' => $this->supportedVersions,
                'requested_version' => $version
            ], 400);
        }

        // Add version to request for use in controllers
        $request->attributes->set('api_version', $version);

        return $next($request);
    }

    /**
     * Extract API version from request
     */
    private function getApiVersion(Request $request): string
    {
        // Check Accept header first (e.g., application/vnd.api+json;version=v1)
        $accept = $request->header('Accept');
        if ($accept && preg_match('/version=([^;,\s]+)/', $accept, $matches)) {
            return $matches[1];
        }

        // Check custom header
        $versionHeader = $request->header('X-API-Version');
        if ($versionHeader) {
            return $versionHeader;
        }

        // Extract from URL path (e.g., /api/v1/...)
        $path = $request->path();
        if (preg_match('/^api\/([^\/]+)/', $path, $matches)) {
            return $matches[1];
        }

        // Default to v1
        return 'v1';
    }
}
