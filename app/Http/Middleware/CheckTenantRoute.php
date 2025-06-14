<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TenantFeatureService;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantRoute
{
    protected TenantFeatureService $featureService;

    public function __construct(TenantFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();
        
        if (!$this->featureService->canAccessRoute($routeName)) {
            return response()->json([
                'error' => 'Access denied',
                'message' => 'This functionality is not available for your account.',
                'route' => $routeName,
                'code' => 'ROUTE_DISABLED'
            ], 403);
        }

        return $next($request);
    }
}