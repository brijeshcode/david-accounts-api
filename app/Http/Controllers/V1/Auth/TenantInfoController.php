<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\TenantFeatureService;

class TenantInfoController extends Controller
{
    public function availableModules(): JsonResponse
    {
        return ApiResponse::index('Tenant Features and modules',  app(TenantFeatureService::class)->getTenantModules());
    }
}