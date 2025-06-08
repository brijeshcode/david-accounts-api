<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\WarehouseStoreRequest;
use App\Http\Requests\V1\Setup\WarehouseUpdateRequest;
use App\Http\Resources\V1\Setup\WarehouseResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    public function index(): JsonResponse
    {
        $warehouses = Warehouse::paginate(10);
        return ApiResponse::index('Warehouses List', WarehouseResource::collection($warehouses));
    }

    public function store(WarehouseStoreRequest $request): JsonResponse
    {
        Warehouse::create($request->validated());
        return ApiResponse::store('Warehouse created successfully');
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        return ApiResponse::send('Warehouse', 200, new WarehouseResource($warehouse));
    }

    public function update(WarehouseUpdateRequest $request, Warehouse $warehouse): JsonResponse
    {
        $warehouse->update($request->validated());
        return ApiResponse::update('Warehouse updated successfully');
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $warehouse->delete();
        return ApiResponse::delete('Warehouse deleted successfully');
    }
}
