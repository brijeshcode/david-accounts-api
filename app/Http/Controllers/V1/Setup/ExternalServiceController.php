<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\ExternalServiceStoreRequest;
use App\Http\Requests\V1\Setup\ExternalServiceUpdateRequest;
use App\Http\Resources\V1\Setup\ExternalServiceResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\ExternalService;
use Illuminate\Http\JsonResponse;

/**
 * @group Setup
 * 
 * @subgroup External services
 * @subgroupDescription This is setup api for external services
 */
class ExternalServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $externalServices = ExternalService::paginate(10);
        return ApiResponse::index('ExternalServices List', ExternalServiceResource::collection($externalServices));
    }

    public function store(ExternalServiceStoreRequest $request): JsonResponse
    {
        ExternalService::create($request->validated());
        return ApiResponse::store('ExternalService created successfully');
    }

    public function show(ExternalService $externalService): JsonResponse
    {
        return ApiResponse::send('ExternalService', 200, new ExternalServiceResource($externalService));
    }

    public function update(ExternalServiceUpdateRequest $request, ExternalService $externalService): JsonResponse
    {
        $externalService->update($request->validated());
        return ApiResponse::update('ExternalService updated successfully');
    }

    public function destroy(ExternalService $externalService): JsonResponse
    {
        $externalService->delete();
        return ApiResponse::delete('ExternalService deleted successfully');
    }
}
