<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\ServiceStoreRequest;
use App\Http\Requests\V1\Setup\ServiceUpdateRequest;
use App\Http\Resources\V1\Setup\ServiceResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    
    public function index(): JsonResponse
    {
        $services = Service::paginate(10);
        return ApiResponse::index('Services List', ServiceResource::collection($services));
    }

    public function store(ServiceStoreRequest $request): JsonResponse
    {
        // dd($request->validated());
        Service::create($request->validated());
        return ApiResponse::store('Service created successfully');
    }

    public function show(Service $service): JsonResponse
    {
        return ApiResponse::send('Service', 200, new ServiceResource($service));
    }

    public function update(ServiceUpdateRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());
        return ApiResponse::update('Service updated successfully');
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();
        return ApiResponse::delete('Service deleted successfully');
    }
}
