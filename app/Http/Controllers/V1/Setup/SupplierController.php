<?php

namespace App\Http\Controllers\V1\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Setup\SupplierStoreRequest;
use App\Http\Requests\V1\Setup\SupplierUpdateRequest;
use App\Http\Resources\V1\Setup\SupplierResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\Supplier;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Setup
 * 
 * @subgroup Supplier
 * @subgroupDescription This is setup api
 */
class SupplierController extends Controller
{
    use HasPagination;

    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query();
        $query->orderBy('created_at', 'desc');
        $suppliers = $this->applyPagination($query, $request);
        return ApiResponse::paginated('Users List', $suppliers, SupplierResource::class);
    }

    public function store(SupplierStoreRequest $request): JsonResponse
    {
        Supplier::create($request->validated());
        return ApiResponse::store('Supplier created successfully');
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return ApiResponse::send('Supplier', 200, new SupplierResource($supplier));
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier->update($request->validated());
        return ApiResponse::update('Supplier updated successfully');
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();
        return ApiResponse::delete('Supplier deleted successfully');
    }
}
