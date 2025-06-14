<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantFeatureService;

class CheckTenantFeature
{
    protected TenantFeatureService $featureService;

    public function __construct(TenantFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (!$this->featureService->hasFeature($feature)) {
            return response()->json([
                'error' => 'Feature not available',
                'message' => "The '{$feature}' feature is not enabled for your account.",
                'code' => 'FEATURE_DISABLED',
                'available_features' => $this->featureService->getTenantFeatures()
            ], 403);
        }

        return $next($request);
    }
}
