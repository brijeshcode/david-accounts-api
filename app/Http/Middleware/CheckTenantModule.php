<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TenantFeatureService;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantModule
{
    protected TenantFeatureService $featureService;

    public function __construct(TenantFeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    public function handle(Request $request, Closure $next, string $module): Response
    {
        
        if (!$this->featureService->hasModule($module)) {
            return response()->json([
                'error' => 'Module not available',
                'message' => "The '{$module}' module is not enabled for your account.",
                'code' => 'MODULE_DISABLED',
                'available_modules' => $this->featureService->getTenantModules()
            ], 403);
        }

        return $next($request);
    }
}
